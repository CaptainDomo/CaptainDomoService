<?php
namespace cds\Model;


use MyCLabs\Enum\Enum;

class RegisterResultCode extends Enum
{
    const NO_MATCHING_MEMBER = "NO_MATCHING_MEMBER";
    const SUBSCRIPTION_ALREADY_EXISTS = "SUBSCRIPTION_ALREADY_EXISTS";
    const SUBSCRIPTION_WITH_OTHER_EMAIL_EXISTS = "SUBSCRIPTION_WITH_OTHER_EMAIL_EXISTS";
    const SUBSCRIPTION_WITH_OTHER_MEMBER_DATA_EXISTS = "SUBSCRIPTION_WITH_OTHER_MEMBER_DATA_EXISTS";
    const SUBSCRIPTION_ADDED = "SUBSCRIPTION_ADDED";
    const TECHNICAL_ERROR = "TECHNICAL_ERROR";
}