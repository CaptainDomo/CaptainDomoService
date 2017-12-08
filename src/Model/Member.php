<?php
namespace cds\Model;

class Member {
    private $id;
	private $number;
	private $firstname;
	private $lastname;

    /**
     * @return mixed
     */
    function getLastname(){
		return $this->lastname;
	}

    /**
     * @param $lastname
     */
    function setLastname($lastname){
		$this->lastname=$lastname;
	}


    /**
     * @return mixed
     */
    function getFirstname(){
		return $this->firstname;
	}

    /**
     * @param $firstname
     */
    function setFirstname($firstname){
		$this->firstname = $firstname;
	}

    /**
     * @return mixed
     */
    function getNumber(){
		return $this->number;
	}

    /**
     * @param $number
     */
    function setNumber($number) {
		$this->number = $number;
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
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}