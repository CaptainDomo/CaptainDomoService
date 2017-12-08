<?php

namespace cds\Controller\Response;


use cds\Model\RegisterResult;
use cds\Model\RegisterResultCode;

class RegistrationResponse
{
    private $registerResultCode;
    private $existingSubscriptions;

    /**
     * @param RegisterResult $registerResult
     * @return array
     */
    public static function toResponse($registerResult){
        $registrationResponse = new RegistrationResponse();
        $registrationResponse->setRegisterResultCode($registerResult->getRegisterResultCode()->getKey());

        $registrationResponse->setExistingSubscriptions(
            SubscriptionResponse::fromListToResponse($registerResult->getExistingSubscriptions()));

        return get_object_vars($registrationResponse);
    }

    /**
     * @param RegisterResultCode $registerResultCode
     * @return RegistrationResponse
     */
    public function setRegisterResultCode($registerResultCode)
    {
        $this->registerResultCode = $registerResultCode;
        return $this;
    }

    /**
     * @param SubscriptionResponse[] $existingSubscriptions
     * @return RegistrationResponse
     */
    public function setExistingSubscriptions($existingSubscriptions)
    {
        $this->existingSubscriptions = $existingSubscriptions;
        return $this;
    }


}