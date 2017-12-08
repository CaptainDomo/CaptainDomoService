<?php

namespace cds\Controller\Response;


use cds\Model\PublishPreCheckedMembersResultCode;

class PublishPreCheckedMembersResponse
{

    private $publishPreCheckedMembersResultCode;

    /**
     * @param PublishPreCheckedMembersResultCode $publishPreCheckedMembersResultCode
     * @return array
     */
    public static function toResponse($publishPreCheckedMembersResultCode)
    {
        $publishPreCheckedMembersResponse = new PublishPreCheckedMembersResponse();
        $publishPreCheckedMembersResponse->setPublishPreCheckedMembersResultCode($publishPreCheckedMembersResultCode->getKey());

        return get_object_vars($publishPreCheckedMembersResponse);
    }

    /**
     * @param PublishPreCheckedMembersResultCode $publishPreCheckedMembersResultCode
     * @return PublishPreCheckedMembersResponse
     */
    public function setPublishPreCheckedMembersResultCode($publishPreCheckedMembersResultCode)
    {
        $this->publishPreCheckedMembersResultCode = $publishPreCheckedMembersResultCode;
        return $this;
    }

    /**
     * @return PublishPreCheckedMembersResultCode
     */
    public function getPublishPreCheckedMembersResultCode()
    {
        return $this->publishPreCheckedMembersResultCode;
    }


}