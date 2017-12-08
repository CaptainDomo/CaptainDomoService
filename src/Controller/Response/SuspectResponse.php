<?php

namespace cds\Controller\Response;


use cds\Model\Suspect;

class SuspectResponse
{
    private $id;
    private $subscriptionRequest;
    private $suspectReason;

    /**
     * @param Suspect $suspect
     * @return array for JSON from SuspectResponse
     */
    private static function toResponse(Suspect $suspect)
    {
        $suspectResponse = new SuspectResponse();

        $suspectResponse->setId($suspect->getId());

        if ($suspect->getSubscriptionRequest() != null) {
            $suspectResponse->setSubscriptionRequest(
                SubscriptionResponse::toResponseFromSubscriptionRequest($suspect->getSubscriptionRequest()));
        }

        if ($suspect->getSuspectReason() != null) {
            $suspectResponse->setSuspectReason($suspect->getSuspectReason());
        }

        return get_object_vars($suspectResponse);
    }

    /**
     * @param $suspects array of Suspect
     * @return array of SuspectRespones within a suspectList
     */
    public static function fromListToResponse($suspects)
    {
        $suspectResponses = [];

        foreach ($suspects as $suspect) {
            $suspectResponses[] = self::toResponse($suspect);
        }

        $suspectList = ['suspectList' => $suspectResponses];

        return $suspectList;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return SuspectResponse
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubscriptionRequest()
    {
        return $this->subscriptionRequest;
    }

    /**
     * @param mixed $subscriptionRequest
     * @return SuspectResponse
     */
    public function setSubscriptionRequest($subscriptionRequest)
    {
        $this->subscriptionRequest = $subscriptionRequest;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSuspectReason()
    {
        return $this->suspectReason;
    }

    /**
     * @param mixed $suspectReason
     * @return SuspectResponse
     */
    public function setSuspectReason($suspectReason)
    {
        $this->suspectReason = $suspectReason;
        return $this;
    }
}