<?php

namespace cds\Model;


class SuspectResolvingResult
{
    private $member;
    private $suspectResolvingResultCode;
    private $subscriptions;

    /**
     * @param SuspectResolvingResultCode $suspectResolvingResultCode
     * @return SuspectResolvingResult
     */
    public function setSuspectResolvingResultCode(SuspectResolvingResultCode $suspectResolvingResultCode)
    {
        $this->suspectResolvingResultCode = $suspectResolvingResultCode;
        return $this;
    }

    /**
     * @return SuspectResolvingResultCode
     */
    public function getSuspectResolvingResultCode()
    {
        return $this->suspectResolvingResultCode;
    }

    /**
     * @param Subscription[] $subscriptions
     * @return SuspectResolvingResult
     */
    public function setSubscriptions($subscriptions)
    {
        $this->subscriptions = $subscriptions;
        return $this;
    }

    /**
     * @return Subscription[]
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * @param Member $member
     * @return SuspectResolvingResult
     */
    public function setMember(Member $member)
    {
        $this->member = $member;
        return $this;
    }
}