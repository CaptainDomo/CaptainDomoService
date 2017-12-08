<?php

namespace cds\Controller\Response;

use cds\Model\SubscriptionDeleteResult;
use cds\Model\SubscriptionDeleteResultCode;

class SubscriptionDeleteResponse
{
    private $subscriptionDeleteResultCode;

    public static function toResponse(SubscriptionDeleteResult $subscriptionDeleteResult)
    {
        $subscriptionDeleteResponse = new SubscriptionDeleteResponse();
        $subscriptionDeleteResponse->setSubscriptionDeleteResultCode(
            $subscriptionDeleteResult->getSubscriptionDeleteResultCode()->getKey());

        return get_object_vars($subscriptionDeleteResponse);
    }

    /**
     * @param SubscriptionDeleteResultCode $subscriptionDeleteResultCode
     * @return SubscriptionDeleteResponse
     */
    public function setSubscriptionDeleteResultCode($subscriptionDeleteResultCode)
    {
        $this->subscriptionDeleteResultCode = $subscriptionDeleteResultCode;
        return $this;
    }
}