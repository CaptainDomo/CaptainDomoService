<?php

namespace cds\Controller\Response;


use cds\Model\SuspectResolvingResult;
use cds\Model\SuspectResolvingResultCode;

class SuspectResolvingResponse
{
    private $suspectResolvingResultCode;
    private $subscriptions;

    /**
     * @param SuspectResolvingResult $suspectResolvingResult
     * @return array
     */
    public static function toResponse(SuspectResolvingResult $suspectResolvingResult){
        $suspectResolvingResponse = new SuspectResolvingResponse();

        $suspectResolvingResponse
            ->setSuspectResolvingResultCode($suspectResolvingResult->getSuspectResolvingResultCode());

        $suspectResolvingResponse
            ->setSubscriptions(SubscriptionResponse::fromListToResponse($suspectResolvingResult->getSubscriptions()));

        return get_object_vars($suspectResolvingResponse);
    }

    /**
     * @param SuspectResolvingResultCode $suspectResolvingResultCode
     * @return SuspectResolvingResponse
     */
    public function setSuspectResolvingResultCode($suspectResolvingResultCode)
    {
        $this->suspectResolvingResultCode = $suspectResolvingResultCode->getKey();
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSuspectResolvingResultCode()
    {
        return $this->suspectResolvingResultCode;
    }

    /**
     * @param mixed $subscriptions
     * @return SuspectResolvingResponse
     */
    public function setSubscriptions($subscriptions)
    {
        $this->subscriptions = $subscriptions;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }
}