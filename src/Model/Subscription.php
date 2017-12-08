<?php

namespace cds\Model;

class Subscription{
	private $id;
	private $emailaddress;
	private $number;
	private $firstname;
	private $lastname;
	private $member;

    /**
     * @return Member
     */
    public function getMember(){
	    return $this->member;
	}

    /**
     * @param Member $member
     */
    public function setMember(Member $member){
	    $this->member = $member;
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
     * @return Subscription{
     */
    public function setId($id){
        $this->id = $id;
        return $this;
    }

    /**
     * emailaddress
     * @return string
     */
    public function getEmailaddress(){
        return $this->emailaddress;
    }

    /**
     * emailaddress
     * @param string $emailaddress
     * @return Subscription{
     */
    public function setEmailaddress($emailaddress){
        $this->emailaddress = $emailaddress;
        return $this;
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
     * @return Subscription
     */
    public function setNumber($number){
        $this->number = $number;
        return $this;
    }

    /**
     * firstname
     * @return string
     */
    public function getFirstname(){
        return $this->firstname;
    }

    /**
     * firstname
     * @param string $firstname
     * @return Subscription
     */
    public function setFirstname($firstname){
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * lastname
     * @return string
     */
    public function getLastname(){
        return $this->lastname;
    }

    /**
     * lastname
     * @param string $lastname
     * @return Subscription
     */
    public function setLastname($lastname){
        $this->lastname = $lastname;
        return $this;
    }

}
?>