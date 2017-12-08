<?php

namespace cds\Model;

use MyCLabs\Enum\Enum;

class SuspectReason extends Enum
{

    const NO_MATCHING_MEMBER = "NO_MATCHING_MEMBER";

    const SUBSCRIPTION_WITH_OTHER_MEMBER_DATA = "SUBSCRIPTION_WITH_OTHER_MEMBER_DATA";
}

?>
