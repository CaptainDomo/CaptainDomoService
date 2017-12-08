<?php

namespace cds\Model;

use MyCLabs\Enum\Enum;

class UnsubscribeResultCode extends Enum
{
    const SUBSCRIPTION_NOT_EXISTENT = "SUBSCRIPTION_NOT_EXISTENT";

    const SUBSCRIPTION_REMOVED = "SUBSCRIPTION_REMOVED";

    const TECHNICAL_ERROR = "TECHNICAL_ERROR";
}