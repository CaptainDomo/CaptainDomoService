<?php

namespace cds\Controller\Response;

use cds\Model\Subscription;
use cds\Model\SubscriptionRequest;

class SubscriptionResponse
{
    private $id;
    private $number;
    private $firstname;
    private $lastname;
    private $email;

    private $member;

    public static function toResponseFromSubscriptionRequest(SubscriptionRequest $subscriptionRequest)
    {
        $subscription = new Subscription();

        $subscription
            ->setNumber($subscriptionRequest->getNumber())
            ->setFirstname($subscriptionRequest->getFirstname())
            ->setLastname($subscriptionRequest->getLastname())
            ->setEmailaddress($subscriptionRequest->getEmail());

        return self::toResponse($subscription);
    }

    /**
     * @param Subscription $subscription
     * @return array
     */
    public static function toResponse(Subscription $subscription)
    {
        $subscriptionResponse = new SubscriptionResponse();

        if ($subscription->getMember() != null) {
            $subscriptionResponse->setMember(MemberResponse::toResponse($subscription->getMember()));
        }

        $subscriptionResponse
            ->setId($subscription->getId())
            ->setNumber($subscription->getNumber())
            ->setFirstname($subscription->getFirstname())
            ->setLastname($subscription->getLastname())
            ->setEmail($subscription->getEmailaddress());

        return get_object_vars($subscriptionResponse);
    }

    /**
     * @param Subscription[] $subscriptions
     * @return array    with only one element named 'subscriptionList'
     */
    public static function fromListToResponse($subscriptions)
    {
        $subscriptionResponses = [];

        if (is_array($subscriptions)) {
            foreach ($subscriptions as $subscription) {
                $subscriptionResponses[] = self::toResponse($subscription);
            }
        }

        $subscriptionList = ['subscriptionList' => $subscriptionResponses];

        return $subscriptionList;
    }

    /**
     * @param mixed $id
     * @return SubscriptionResponse
     */
    public
    function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param mixed $number
     * @return SubscriptionResponse
     */
    public
    function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @param mixed $firstname
     * @return SubscriptionResponse
     */
    public
    function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @param mixed $lastname
     * @return SubscriptionResponse
     */
    public
    function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @param mixed $email
     * @return SubscriptionResponse
     */
    public
    function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param mixed $member
     * @return SubscriptionResponse
     */
    public
    function setMember($member)
    {
        $this->member = $member;
        return $this;
    }

}