<?php
namespace cds\Model;

class Suspect{
	private $id;
	private $subscriptionRequest;
	private $suspectReason;
	

    /**
     * subscriptionRequest
     * @return SubscriptionRequest
     */
    public function getSubscriptionRequest(){
        return $this->subscriptionRequest;
    }

    /**
     * subscriptionRequest
     * @param SubscriptionRequest $subscriptionRequest
     * @return Suspect{
     */
    public function setSubscriptionRequest($subscriptionRequest){
        $this->subscriptionRequest = $subscriptionRequest;
        return $this;
    }

    /**
     * suspectReason
     * @return SuspectReason
     */
    public function getSuspectReason(){
        return $this->suspectReason;
    }

    /**
     * suspectReason
     * @param String $suspectReason
     * @return Suspect{
     */
    public function setSuspectReason($suspectReason){
        $this->suspectReason = $suspectReason;
        return $this;
    }


    /**
     * id
     * @return int
     */
    public function getId(){
        return $this->id;
    }

    /**
     * id
     * @param int $id
     * @return Suspect{
     */
    public function setId($id){
        $this->id = $id;
        return $this;
    }

}

?>
