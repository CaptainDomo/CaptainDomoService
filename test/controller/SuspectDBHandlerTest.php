<?php
require '../../vendor/autoload.php';

use cds\Database\SuspectDBHandler;
use cds\Model\SubscriptionRequest;
use cds\Model\Suspect;
use cds\Model\SuspectReason;


class SuspectDBHandlerTest extends PHPUnit_Framework_TestCase
{

    public function testAddSuspect()
    {
        $suspectDBHandler = new SuspectDBHandler();

        $expected = new Suspect();
        $subscriptionRequest = new SubscriptionRequest("748", "Lena", "August", "Lena.August@jvm.de");
        $expected->setSubscriptionRequest($subscriptionRequest);
        $expected->setSuspectReason(SuspectReason::NO_MATCHING_MEMBER);

        $expected = $suspectDBHandler->add($expected);

        $this->assertNotNull($expected);
        $this->assertNotNull($expected->getId());

        $suspect = $suspectDBHandler->get($expected->getId());

        $this->assertEquals($expected, $suspect);
    }
}

?>