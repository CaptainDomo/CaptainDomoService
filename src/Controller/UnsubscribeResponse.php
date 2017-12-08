<?php

namespace cds\Controller;


use cds\Model\UnsubscribeResult;
use cds\Model\UnsubscribeResultCode;

class UnsubscribeResponse
{
    private $unsubscribeResultCode;

    /**
     * @param UnsubscribeResult $unsubscribeResult
     * @return array
     */
    public static function toResponse(UnsubscribeResult $unsubscribeResult){
        $unsubscribeResponse = new UnsubscribeResponse();
        $unsubscribeResponse->setUnsubscribeResultCode($unsubscribeResult->getUnsubscribeResultCode()->getKey());

        return get_object_vars($unsubscribeResponse);
    }

    /**
     * @param UnsubscribeResultCode $unsubscribeResultCode
     * @return UnsubscribeResponse
     */
    public function setUnsubscribeResultCode($unsubscribeResultCode)
    {
        $this->unsubscribeResultCode = $unsubscribeResultCode;
        return $this;
    }

    /**
     * @return UnsubscribeResultCode
     */
    public function getUnsubscribeResultCode()
    {
        return $this->unsubscribeResultCode;
    }

}