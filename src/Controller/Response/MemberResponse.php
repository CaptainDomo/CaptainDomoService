<?php

namespace cds\Controller\Response;


use cds\Model\Member;

class MemberResponse
{
    private $id;
    private $number;
    private $firstname;
    private $lastname;

    public static function fromListToResponse(array $members)
    {
        $memberList=[];

        foreach ($members as $member) {
            $memberList[]=self::toResponse($member);
        }

        $memberReponseList = ['memberList' => $memberList];

        return $memberReponseList;
    }

    public static function toResponse(Member $member){
        $memberResponse = new MemberResponse();

        $memberResponse
            ->setId($member->getId())
            ->setNumber($member->getNumber())
            ->setFirstname($member->getFirstname())
            ->setLastname($member->getLastname());

        return get_object_vars($memberResponse);
    }

    /**
     * @param mixed $id
     * @return MemberResponse
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param mixed $number
     * @return MemberResponse
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @param mixed $firstname
     * @return MemberResponse
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @param mixed $lastname
     * @return MemberResponse
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }
}