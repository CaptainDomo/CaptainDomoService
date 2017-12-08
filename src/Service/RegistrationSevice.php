<?php

namespace cds\Service;


use cds\Database\MemberDBHandler;
use cds\Model\Member;
use cds\Model\RegisterResult;
use cds\Model\RegisterResultCode;
use cds\Model\SubscriptionDeleteResultCode;
use cds\Model\SubscriptionRequest;
use cds\Model\SubscriptionSearchResultCode;
use cds\Model\SuspectReason;
use cds\Model\UnsubscribeRequest;
use cds\Model\UnsubscribeResult;
use cds\Model\UnsubscribeResultCode;

class RegistrationSevice
{
    private $memberDBHandler;

    private $majorDomoService;

    private $subscriptionService;

    private $suspectService;

    function __construct()
    {
        $this->majorDomoService = new MajorDomoService();
        $this->memberDBHandler = new MemberDBHandler();
        $this->subscriptionService = new SubscriptionService();
        $this->suspectService = new SuspectService();
    }

    /**
     * @param SubscriptionRequest $subscriptionRequest
     * @return RegisterResult
     */
    function addAdditionalEmail($subscriptionRequest)
    {
        $registerResult = new RegisterResult();

        $subscriptionSearchResult = $this->subscriptionService->add($subscriptionRequest, null);

        if ($subscriptionSearchResult->getResultCode() != SubscriptionSearchResultCode::SUBSCRIPTION_ADDED) {
            $registerResult->setRegisterResultCode(new RegisterResultCode(RegisterResultCode::TECHNICAL_ERROR));
            return $registerResult;
        }

        $registerResult->setRegisterResultCode(new RegisterResultCode(RegisterResultCode::SUBSCRIPTION_ADDED));
        $registerResult->setExistingSubscriptions($subscriptionSearchResult->getSubscriptions());

        return $registerResult;
    }

    /**
     * @param SubscriptionRequest $subscriptionRequest
     * @return RegisterResult
     */
    function register($subscriptionRequest)
    {
        $registerResult = new RegisterResult();

        $memberId = $this->memberDBHandler->exactMatch(
            $subscriptionRequest->getNumber(),
            $subscriptionRequest->getFirstname(),
            $subscriptionRequest->getLastname()
        );

        if ($memberId == null) { // Member could not be matched;
            // SubscriptionRequest is stored as Suspect and
            // has to be validated by administrator
            // (receives mail)
            $this->addSuspectBecauseOfNoMatchingMember($registerResult, $subscriptionRequest);
        } else {
            $member = $this->memberDBHandler->getById($memberId);
            $subscriptionSearchResult = $this->subscriptionService->searchSubscription($subscriptionRequest->getEmail(), $member);

            switch ($subscriptionSearchResult->getResultCode()) {
                case SubscriptionSearchResultCode::SUBSCRIPTION_ALREADY_EXISTS:
                    $registerResult->setRegisterResultCode(new RegisterResultCode(RegisterResultCode::SUBSCRIPTION_ALREADY_EXISTS));
                    break;
                case SubscriptionSearchResultCode::SUBSCRIPTION_WITH_OTHER_EMAIL_EXISTS:
                    $registerResult->setRegisterResultCode(new RegisterResultCode(RegisterResultCode::SUBSCRIPTION_WITH_OTHER_EMAIL_EXISTS));
                    $registerResult->setExistingSubscriptions($subscriptionSearchResult->getSubscriptions());
                    break;
                case SubscriptionSearchResultCode::NO_MATCHING_MEMBER:
                    $this->addSuspectBecauseOfNoMatchingMember($registerResult, $subscriptionRequest);
                    break;
                case SubscriptionSearchResultCode::SUBSCRIPTION_WITH_OTHER_MEMBER_DATA_EXISTS:
                    $this->suspectService->addSuspectFrom($subscriptionRequest, new SuspectReason(SuspectReason::SUBSCRIPTION_WITH_OTHER_MEMBER_DATA));
                    $registerResult->setRegisterResultCode(new RegisterResultCode(RegisterResultCode::SUBSCRIPTION_WITH_OTHER_MEMBER_DATA_EXISTS));

                    break;
                case SubscriptionSearchResultCode::SUBSCRIPTION_NOT_EXISTENT:
                    $subscriptionSearchResult = $this->subscriptionService->add($subscriptionRequest, $member);

                    if ($subscriptionSearchResult->getResultCode() != SubscriptionSearchResultCode::SUBSCRIPTION_ADDED) {
                        $registerResult->setRegisterResultCode(new RegisterResultCode(RegisterResultCode::TECHNICAL_ERROR));
                        return $registerResult;
                    }

                    $registerResult->setRegisterResultCode(new RegisterResultCode(RegisterResultCode::SUBSCRIPTION_ADDED));
                    $registerResult->setExistingSubscriptions($subscriptionSearchResult->getSubscriptions());
                    break;
            }
        }

        return $registerResult;
    }

    /**
     * @param RegisterResult $registerResult
     * @param SubscriptionRequest $subscriptionRequest
     */
    private function addSuspectBecauseOfNoMatchingMember($registerResult, $subscriptionRequest)
    {
        $registerResult
            ->setRegisterResultCode(new RegisterResultCode(RegisterResultCode::NO_MATCHING_MEMBER));

        $this->suspectService->addSuspectFrom($subscriptionRequest, new SuspectReason(SuspectReason::NO_MATCHING_MEMBER));
    }

    /**
     * @param UnsubscribeRequest $unsubscribeRequest
     * @return UnsubscribeResult
     */
    public function unsubscribe(UnsubscribeRequest $unsubscribeRequest)
    {
        $unsubscribeResult = new UnsubscribeResult();
        $fake_member = new Member();

        $subscriptionSearchResult = $this->subscriptionService->searchSubscription(
            $unsubscribeRequest->getEmail(),
            $fake_member
        );

        switch ($subscriptionSearchResult->getResultCode()) {
            //That's because we are using a fake member; actually this means we have a subscription with this email
            case SubscriptionSearchResultCode::SUBSCRIPTION_WITH_OTHER_MEMBER_DATA_EXISTS:
                $subscriptionDeleteResult = $this->subscriptionService->unsubscribe($subscriptionSearchResult->getSubscriptions()[0]);

                switch ($subscriptionDeleteResult->getSubscriptionDeleteResultCode()){
                    case SubscriptionDeleteResultCode::SUCCESSFUL:
                        $unsubscribeResult->setUnsubscribeResultCode(new UnsubscribeResultCode(UnsubscribeResultCode::SUBSCRIPTION_REMOVED));
                        break;
                    default:
                        $unsubscribeResult->setUnsubscribeResultCode(new UnsubscribeResultCode(UnsubscribeResultCode::TECHNICAL_ERROR));
                        break;
                }

                break;
            default:
                $unsubscribeResult->setUnsubscribeResultCode(new UnsubscribeResultCode(UnsubscribeResultCode::SUBSCRIPTION_NOT_EXISTENT));
                break;
        }

        return $unsubscribeResult;
    }
}