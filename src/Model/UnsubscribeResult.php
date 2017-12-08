<?php

namespace cds\Model;


class UnsubscribeResult
{

    private $unsubscribeResultCode;

    /**
     * UnsubscribeResult constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param UnsubscribeResultCode $unsubscribeResultCode
     * @return UnsubscribeResult
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