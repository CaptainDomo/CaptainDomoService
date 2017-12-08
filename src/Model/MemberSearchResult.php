<?php

namespace cds\Model;

class MemberSearchResult
{
    private $memberSearchResultCode;
    private $member;

    public function __construct($memberSearchResultCode, $member){
        $this->setMemberSearchResultCode($memberSearchResultCode);
        $this->setMember($member);
    }

    /**
     * @return MemberSearchResultCode
     */
    public function getMemberSearchResultCode()
    {
        return $this->memberSearchResultCode;
    }

    /**
     * @param MemberSearchResultCode $memberSearchResultCode
     * @return MemberSearchResult
     */
    public function setMemberSearchResultCode($memberSearchResultCode)
    {
        $this->memberSearchResultCode = $memberSearchResultCode;
        return $this;
    }

    /**
     * @return Member
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @param Member $member
     * @return MemberSearchResult
     */
    public function setMember($member)
    {
        $this->member = $member;
        return $this;
    }
}