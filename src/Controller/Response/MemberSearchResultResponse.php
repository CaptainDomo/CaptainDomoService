<?php
namespace cds\Controller\Response;


use cds\Model\MemberSearchResult;
use cds\Model\MemberSearchResultCode;

class MemberSearchResultResponse
{
    private $memberSearchResultCode;
    private $member;

    /**
     * @param $memberSearchResults
     * @return array of MemberSearchResultResponse as memberSearchResultList JSON
     */
    public static function fromListToResponse($memberSearchResults)
    {
        $memberSearchResultResponses = [];

        foreach ($memberSearchResults as $memberSearchResult) {
            $memberSearchResultResponses[] = self::toReponse($memberSearchResult);
        }

        return $memberSearchResultResponseList = ['memberSearchResultList' => $memberSearchResultResponses];
    }

    /**
     * @param MemberSearchResult $memberSearchResult
     * @return MemberSearchResultResponse
     */
    private static function toReponse(MemberSearchResult $memberSearchResult)
    {
        $memberSearchResultResponse = new MemberSearchResultResponse();

        $memberSearchResultResponse
            ->setMemberSearchResultCode($memberSearchResult->getMemberSearchResultCode())
            ->setMember(MemberResponse::toResponse(
                $memberSearchResult->getMember()));

        return get_object_vars($memberSearchResultResponse);
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
     * @return MemberSearchResultResponse
     */
    public function setMemberSearchResultCode($memberSearchResultCode)
    {
        $this->memberSearchResultCode = $memberSearchResultCode;
        return $this;
    }

    /**
     * @return MemberResponse
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @param properties array of Member
     * @return MemberSearchResultResponse
     */
    public function setMember($member)
    {
        $this->member = $member;
        return $this;
    }
}