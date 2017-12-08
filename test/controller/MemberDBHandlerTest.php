<?php
require '../../vendor/autoload.php';

use cds\Database\MemberDBHandler;
use cds\Model\Member;

class MemberDBHandlerTest extends PHPUnit_Framework_TestCase
{

    public function testSuccessfulGet()
    {
        $memberDBHandler = new MemberDBHandler();
        $expected = new Member();
        $expected->setNumber(23);
        $expected->setFirstname("Philipp");
        $expected->setLastname("Feustel");

        $member = $memberDBHandler->getById(1);

        $this->assertEquals($expected, $member);
    }

    public function testUnsuccessfulGet()
    {
        $memberDBHandler = new MemberDBHandler();
        $expected = new Member();
        $expected->setNumber(2);
        $expected->setFirstname("Philipp");
        $expected->setLastname("Feustel");

        $member = $memberDBHandler->getById(1);

        $this->assertNotEquals($expected, $member);
    }

    public function testSuccessfulExactMatch()
    {
        $memberDBHandler = new MemberDBHandler();
        $id = $memberDBHandler->exactMatch(23, "Philipp", "Feustel");

        $this->assertNotNull($id);
        $this->assertEquals(1, $id);
    }

    public function testSuccessfulIgnoreCaseExactMatch()
    {
        $memberDBHandler = new MemberDBHandler();
        $id = $memberDBHandler->exactMatch(23, "pHiLiPp", "fEuStEl");

        $this->assertNotNull($id);
        $this->assertEquals(1, $id);
    }

    public function testUnsuccessfulExactMatchForNumber()
    {
        $memberDBHandler = new MemberDBHandler();
        $id = $memberDBHandler->exactMatch(3, "pHiLiPp", "fEuStEl");

        $this->assertNull($id);
    }

    public function testUnsuccessfulExactMatchForFirstname()
    {
        $memberDBHandler = new MemberDBHandler();
        $id = $memberDBHandler->exactMatch(23, "pHiLip", "fEuStEl");

        $this->assertNull($id);
    }

    public function testUnsuccessfulExactMatchForLastname()
    {
        $memberDBHandler = new MemberDBHandler();
        $id = $memberDBHandler->exactMatch(23, "pHiLipP", "fEutEl");

        $this->assertNull($id);
    }

    public function testSuccessfulGetPreCheckMemberList()
    {
        $memberDBHandler = new MemberDBHandler();

        $members = $memberDBHandler->getPreCheckMemberList("1453479764");

        $this->assertNotNull($members);
    }
}

?>
