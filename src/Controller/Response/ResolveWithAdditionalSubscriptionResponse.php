<?php

namespace cds\Controller\Response;


use cds\Model\ResolveWithAdditionalSubscriptionResultCode;

class ResolveWithAdditionalSubscriptionResponse
{

    public $resolveWithAdditionalSubscriptionResultCode;

    /**
     * @param ResolveWithAdditionalSubscriptionResultCode $resolveWithAdditionalSubscriptionResultCode
     * @return ResolveWithAdditionalSubscriptionResponse
     */
    public function setResolveWithAdditionalSubscriptionResultCode(
        ResolveWithAdditionalSubscriptionResultCode $resolveWithAdditionalSubscriptionResultCode
    )
    {
        $this->resolveWithAdditionalSubscriptionResultCode = $resolveWithAdditionalSubscriptionResultCode->getKey();
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResolveWithAdditionalSubscriptionResultCode()
    {
        return $this->resolveWithAdditionalSubscriptionResultCode;
    }
}