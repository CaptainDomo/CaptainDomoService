<?php

namespace cds\Model;


class RegisterResult
{
    private $registerResultCode;
    private $existingSubscriptions;

    /**
     * @param RegisterResultCode $registerResultCode
     * @return RegisterResult
     */
    public function setRegisterResultCode($registerResultCode)
    {
        $this->registerResultCode = $registerResultCode;
        return $this;
    }


    /**
     * @param Subscription[] $existingSubscriptions
     * @return RegisterResult
     */
    public function setExistingSubscriptions($existingSubscriptions)
    {
        $this->existingSubscriptions = $existingSubscriptions;
        return $this;
    }

    /**
     * @return Subscription[]
     */
    public function getExistingSubscriptions()
    {
        return $this->existingSubscriptions;
    }

    /**
     * @return RegisterResultCode
     */
    public function getRegisterResultCode()
    {
        return $this->registerResultCode;
    }

}