<?php
namespace cds\Model;


use MyCLabs\Enum\Enum;

class ResolveWithAdditionalSubscriptionResultCode extends Enum
{
    const EMAIL_NOT_VALID = "EMAIL_NOT_VALID";
    const MEMBER_NOT_FOUND = "MEMBER_NOT_FOUND";
    const RESOLVING_FAILED = "RESOLVING_FAILED";
    const SUBSCRIPTION_ADDED = "SUBSCRIPTION_ADDED";
    const SUSPECT_NOT_FOUND = "SUSPECT_NOT_FOUND";

}