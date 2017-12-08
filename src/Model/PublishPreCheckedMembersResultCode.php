<?php

namespace cds\Model;

use MyCLabs\Enum\Enum;

class PublishPreCheckedMembersResultCode extends Enum
{
    const SUCCESSFUL = "SUCCESSFUL";
    const FAILED = "FAILED";
}