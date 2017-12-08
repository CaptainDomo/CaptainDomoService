<?php
namespace cds\Model;

class SubscriptionRequest{
	private $number;
	private $firstname;
	private $lastname;
	private $email;

    /**
     * SubscriptionRequest constructor.
     * @param $number
     * @param $firstname
     * @param $lastname
     * @param $email
     */
    public function __construct($number, $firstname, $lastname, $email){
		$this->setNumber($number);
		$this->setFirstname($firstname);
		$this->setLastname($lastname);
		$this->setEmail($email);
	}

    /**
     * number
     * @return int
     */
    public function getNumber(){
        return $this->number;
    }

    /**
     * number
     * @param int $number
     * @return SubscriptionRequest{
     */
    public function setNumber($number){
        $this->number = $number;
        return $this;
    }

    /**
     * firstname
     * @return String
     */
    public function getFirstname(){
        return $this->firstname;
    }

    /**
     * firstname
     * @param String $firstname
     * @return SubscriptionRequest{
     */
    public function setFirstname($firstname){
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * lastname
     * @return String
     */
    public function getLastname(){
        return $this->lastname;
    }

    /**
     * lastname
     * @param String $lastname
     * @return SubscriptionRequest{
     */
    public function setLastname($lastname){
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * email
     * @return String
     */
    public function getEmail(){
        return $this->email;
    }

    /**
     * email
     * @param String $email
     * @return SubscriptionRequest{
     */
    public function setEmail($email){
        $this->email = $email;
        return $this;
    }

}

?>