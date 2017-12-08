<?php

namespace cds\Model;


class UnsubscribeRequest
{
    private $email;

    /**
     * UnsubscribeRequest constructor.
     * @param $email
     */
    public function __construct($email)
    {
        $this->setEmail($email);
    }

    /**
     * @param mixed $email
     * @return UnsubscribeRequest
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }
}