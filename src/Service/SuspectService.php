<?php

namespace cds\Service;


use cds\Config\Config;
use cds\Database\MemberDBHandler;
use cds\Database\SuspectDBHandler;
use cds\Model\Member;
use cds\Model\SubscriptionRequest;
use cds\Model\SubscriptionSearchResultCode;
use cds\Model\Suspect;
use cds\Model\SuspectReason;
use cds\Model\SuspectResolvingResult;
use cds\Model\SuspectResolvingResultCode;

class SuspectService
{
    private $mailService;
    private $memberDbHandler;
    private $suspectDbHandler;
    private $subscriptionService;

    function __construct()
    {
        $this->mailService = new MailService();
        $this->memberDbHandler = new MemberDBHandler();
        $this->suspectDbHandler = new SuspectDBHandler();
        $this->subscriptionService = new SubscriptionService();
    }

    function getAllSuspects()
    {
        return $this->suspectDbHandler->getAllSuspects();
    }

    public function getSuspect($suspectId)
    {
        return $this->suspectDbHandler->get($suspectId);
    }

    /**
     * @param SubscriptionRequest $subscriptionRequest
     * @param SuspectReason $suspectReason
     * @return Suspect|null
     */
    function addSuspectFrom(SubscriptionRequest $subscriptionRequest, SuspectReason $suspectReason)
    {
        $suspect = new Suspect();
        $suspect->setSubscriptionRequest($subscriptionRequest);
        $suspect->setSuspectReason($suspectReason);

        // Add suspect in DB and get id
        $suspect = $this->suspectDbHandler->add($suspect);

        if ($suspect == null)
            return null;

        // Send email to admin
        $from = Config::get()['SuspectService']['admin_mail_to'];
        $to = Config::get()['SuspectService']['admin_mail_to'];
        $subject = Config::get()['SuspectService']['check_subscription_mail_subject'];
        $manage_suspect_url = Config::get()['SuspectService']['manage_suspect_url'];
        $mail_msg = sprintf(
            Config::get()['SuspectService']['check_subscription_mail_body'],
            $suspect->getSubscriptionRequest()->getFirstname(),
            $suspect->getSubscriptionRequest()->getLastname(),
            $suspect->getSubscriptionRequest()->getNumber(),
            $suspect->getSubscriptionRequest()->getEmail(),
            $suspect->getSuspectReason(), $manage_suspect_url);

        if (!$this->mailService->sendEmail($from, $to, $subject, $mail_msg))
            return null;

        return $suspect;
    }

    /**
     * @param Suspect $suspect
     * @return SuspectResolvingResult
     */
    function reject($suspect)
    {
        $suspectResolvingResult = new SuspectResolvingResult();

        if ($suspect == null) {
            return $suspectResolvingResult->setSuspectResolvingResultCode(
                new SuspectResolvingResultCode(SuspectResolvingResultCode::SUSPECT_NOT_FOUND));
        }

        // Send email to requestor
        $from = Config::get()['SuspectService']['admin_mail_to'];
        $to = $suspect->getSubscriptionRequest()->getEmail();
        $subject = Config::get()['SuspectService']['reject_subscription_request_subject'];
        $mail_msg = Config::get()['SuspectService']['reject_subscription_request_mail_body'];

        if (!$this->mailService->sendEmail($from, $to, $subject, $mail_msg))
            return $suspectResolvingResult->setSuspectResolvingResultCode(
                new SuspectResolvingResultCode(SuspectResolvingResultCode::TECHNICAL_ERROR));

        $this->suspectDbHandler->delete($suspect->getId());

        $suspectResolvingResult->setSuspectResolvingResultCode(
            new SuspectResolvingResultCode(SuspectResolvingResultCode::SUSPECT_REJECTED_SUCCESSFULLY)
        );

        return $suspectResolvingResult;
    }

    /**
     * @param $suspect
     * @return SuspectResolvingResult
     */
    function registerAndAddNewMember(Suspect $suspect){
        $subscriptionRequest = $suspect->getSubscriptionRequest();

        // Add new member
        $member = new Member();
        $member->setNumber($subscriptionRequest->getNumber());
        $member->setFirstname($subscriptionRequest->getFirstname());
        $member->setLastname($subscriptionRequest->getLastname());
        $this->memberDbHandler->addMember($member);

        return $this->resolveSuspectWithMember($suspect, $member);
    }

    function resolveSuspectWithMember(Suspect $suspect, Member $member){
        $suspectResolvingResult = new SuspectResolvingResult();

        $subscriptionRequest = new SubscriptionRequest(
            $member->getNumber(),
            $member->getFirstname(),
            $member->getLastname(),
            $suspect->getSubscriptionRequest()->getEmail());

        $subscriptionSearchResult = $this->subscriptionService->resolveSubscriptionRequest($member, $subscriptionRequest);

        $suspectResolvingResult->setMember($member);
        $suspectResolvingResult->setSubscriptions($subscriptionSearchResult->getSubscriptions());

        switch ($subscriptionSearchResult->getResultCode()) {
            case  SubscriptionSearchResultCode::NO_MATCHING_MEMBER:
                $suspectResolvingResult->setSuspectResolvingResultCode(
                    new SuspectResolvingResultCode(SuspectResolvingResultCode::MEMBER_NOT_FOUND)
                );
                break;
            case  SubscriptionSearchResultCode::SUBSCRIPTION_ALREADY_EXISTS:
                $suspectResolvingResult->setSuspectResolvingResultCode(
                    new SuspectResolvingResultCode(SuspectResolvingResultCode::SUBSCRIPTION_ALREADY_EXISTS)
                );
                break;
            case  SubscriptionSearchResultCode::SUBSCRIPTION_WITH_OTHER_EMAIL_EXISTS:
                $suspectResolvingResult->setSuspectResolvingResultCode(
                    new SuspectResolvingResultCode(SuspectResolvingResultCode::SUBSCRIPTION_WITH_OTHER_EMAIL_EXISTS)
                );
                break;
            case  SubscriptionSearchResultCode::SUBSCRIPTION_WITH_OTHER_MEMBER_DATA_EXISTS:
                $suspectResolvingResult->setSuspectResolvingResultCode(
                    new SuspectResolvingResultCode(SuspectResolvingResultCode::SUBSCRIPTION_WITH_OTHER_MEMBER_DATA_EXISTS)
                );
                break;
            case  SubscriptionSearchResultCode::SUBSCRIPTION_ADDED:
                if (!$this->delete($suspect)) {
                    $suspectResolvingResult->setSuspectResolvingResultCode(
                        new SuspectResolvingResultCode(SuspectResolvingResultCode::TECHNICAL_ERROR));
                } else {
                    $suspectResolvingResult->setSuspectResolvingResultCode(
                        new SuspectResolvingResultCode(SuspectResolvingResultCode::SUBSCRIPTION_ADDED));
                }
                break;
            default:
                $suspectResolvingResult->setSuspectResolvingResultCode(
                    new SuspectResolvingResultCode(SuspectResolvingResultCode::TECHNICAL_ERROR));
        }

        return $suspectResolvingResult;
    }

    /**
     * @param Suspect $suspect
     * @return bool|null
     */
    public function delete(Suspect $suspect)
    {
        return $this->suspectDbHandler->delete($suspect->getId());
    }
}