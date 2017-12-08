<?php
namespace cds\Model;


use MyCLabs\Enum\Enum;

class SubscriptionDeleteResultCode extends Enum
{
    const SUBSCRIPTION_NOT_FOUND = "SUBSCRIPTION_NOT_FOUND";

    const TECHNICAL_ERROR = "TECHNICAL_ERROR";

    const SUCCESSFUL = "SUCCESSFUL";

}