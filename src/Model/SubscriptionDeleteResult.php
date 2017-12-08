<?php

namespace cds\Model;


class SubscriptionDeleteResult
{

    private $subscriptionDeleteResultCode;

    /**
     * @param SubscriptionDeleteResultCode $subscriptionDeleteResultCode
     * @return SubscriptionDeleteResult
     */
    public function setSubscriptionDeleteResultCode(SubscriptionDeleteResultCode $subscriptionDeleteResultCode)
    {
        $this->subscriptionDeleteResultCode = $subscriptionDeleteResultCode;
        return $this;
    }

    /**
     * @return SubscriptionDeleteResultCode
     */
    public function getSubscriptionDeleteResultCode()
    {
        return $this->subscriptionDeleteResultCode;
    }
}