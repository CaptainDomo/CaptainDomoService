<?php
namespace cds\Model;

class SubscriptionSearchResult
{
    private $resultCode;
    private $subscriptions;

    /**
     * resultCode
     * @return SubscriptionSearchResultCode
     */
    public function getResultCode()
    {
        return $this->resultCode;
    }

    /**
     * resultCode
     * @param SubscriptionSearchResultCode $resultCode
     * @return SubscriptionSearchResult{
     */
    public function setResultCode($resultCode)
    {
        $this->resultCode = $resultCode;
        return $this;
    }

    /**
     * subscription
     * @return Subscription[]
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * subscription
     * @param Subscription[] $subscriptions
     * @return SubscriptionSearchResult
     */
    public function setSubscriptions(array $subscriptions)
    {
        $this->subscriptions = $subscriptions;
        return $this;
    }

}

?>

