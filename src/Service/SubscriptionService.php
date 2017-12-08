<?php

namespace cds\Service;

use cds\Database\SubscriptionDBHandler;
use cds\Model\Member;
use cds\Model\Subscription;
use cds\Model\SubscriptionDeleteResult;
use cds\Model\SubscriptionDeleteResultCode;
use cds\Model\SubscriptionRequest;
use cds\Model\SubscriptionSearchResult;
use cds\Model\SubscriptionSearchResultCode;

class SubscriptionService
{
    private $subscriptionDbHandler;

    private $majorDomoService;

    function __construct()
    {
        $this->majorDomoService = new MajorDomoService();
        $this->subscriptionDbHandler = new SubscriptionDBHandler();
    }

    /**
     * @param Member $member
     * @param SubscriptionRequest $subscriptionRequest
     * @return SubscriptionSearchResult|null
     */
    public function addAdditionalSubscriptionForMember(Member $member, SubscriptionRequest $subscriptionRequest)
    {
        $subscriptionSearchResult = $this->subscriptionDbHandler->searchSubscription($subscriptionRequest->getEmail(), $member);

        // In case of:
        // SUBSCRIPTION_WITH_OTHER_MEMBER_DATA_EXISTS
        // SUBSCRIPTION_ALREADY_EXISTS
        // just return the result for further processing

        if ($subscriptionSearchResult->getResultCode() == SubscriptionSearchResultCode::SUBSCRIPTION_NOT_EXISTENT ||
            $subscriptionSearchResult->getResultCode() == SubscriptionSearchResultCode::SUBSCRIPTION_WITH_OTHER_EMAIL_EXISTS
        ) {
            $subscriptionSearchResult = $this->add($subscriptionRequest, $member);
        }

        return $subscriptionSearchResult;
    }

    /**
     * @param SubscriptionRequest $subscriptionRequest
     * @param $member
     * @return SubscriptionSearchResult|null
     */
    public function add(SubscriptionRequest $subscriptionRequest, $member)
    {
        $subscriptionSearchResult = new SubscriptionSearchResult();

        if (!$this->majorDomoService->subscribe($subscriptionRequest->getEmail())) {
            $subscriptionSearchResult->setResultCode(
                new SubscriptionSearchResultCode(SubscriptionSearchResultCode::TECHNICAL_ERROR)
            );
            return $subscriptionSearchResult;
        }

        $subscriptionSearchResult = $this->subscriptionDbHandler->add($subscriptionRequest, $member);

        return $subscriptionSearchResult;
    }

    /**
     * @param Member $member
     * @param SubscriptionRequest $subscriptionRequest
     * @return SubscriptionSearchResult
     */
    public function resolveSubscriptionRequest(Member $member, SubscriptionRequest $subscriptionRequest)
    {
        $subscriptionSearchResult = $this->subscriptionDbHandler->searchSubscription($subscriptionRequest->getEmail(), $member);

        // In case of:
        // SUBSCRIPTION_WITH_OTHER_MEMBER_DATA_EXISTS
        // SUBSCRIPTION_ALREADY_EXISTS
        // SUBSCRIPTION_WITH_OTHER_EMAIL_EXISTS
        // just return the result for further processing

        if ($subscriptionSearchResult->getResultCode() == SubscriptionSearchResultCode::SUBSCRIPTION_NOT_EXISTENT) {
            // This should be the standard case
            $subscriptionSearchResult = $this->add($subscriptionRequest, $member);
        }

        return $subscriptionSearchResult;
    }

    /**
     * @param $email
     * @param Member $member
     * @return SubscriptionSearchResult
     */
    public function searchSubscription($email, Member $member)
    {
        return $this->subscriptionDbHandler->searchSubscription($email, $member);
    }

    /**
     * @param Subscription $subscription
     * @return SubscriptionDeleteResult
     */
    public function unsubscribe(Subscription $subscription)
    {
        $subscriptionDeleteResult = new SubscriptionDeleteResult();

        if (
            !$this->majorDomoService->unsubscribe($subscription->getEmailaddress()) ||
            !$this->subscriptionDbHandler->delete($subscription)
        ) {
            $subscriptionDeleteResult->setSubscriptionDeleteResultCode(
                new SubscriptionDeleteResultCode(SubscriptionDeleteResultCode::TECHNICAL_ERROR));

            return $subscriptionDeleteResult;
        }

        $subscriptionDeleteResult->setSubscriptionDeleteResultCode(
            new SubscriptionDeleteResultCode(SubscriptionDeleteResultCode::SUCCESSFUL));

        return $subscriptionDeleteResult;
    }
}