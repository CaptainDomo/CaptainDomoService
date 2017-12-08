<?php
require '../../vendor/autoload.php';

use cds\Database\SubscriptionDBHandler;
use cds\Model\Member;
use cds\Model\Subscription;
use cds\Model\SubscriptionSearchResult;
use cds\Model\SubscriptionSearchResultCode;


class SubscriptionDBHandlerTest extends PHPUnit_Framework_TestCase{
	
	public function testExactMatch(){
		$subscriptionDBHandler = new SubscriptionDBHandler();
		
		$member =  new Member();
        $member->setNumber(23);
        $member->setFirstname("Philipp");
        $member->setLastname("Feustel");
		
		
		$subscription = new Subscription();
		$subscription->setId ( 1 );
		$subscription->setEmailaddress ( "phfeustel@gmx.de" );
		$subscription->setNumber ( 23 );
		$subscription->setFirstname ( "Philipp" );
		$subscription->setLastname ( "Feustel" );
        $subscriptions[]=$subscription;
		
		$expected = new SubscriptionSearchResult();
		$expected->setSubscriptions($subscriptions);
		$expected->setResultCode(SubscriptionSearchResultCode::SUBSCRIPTION_ALREADY_EXISTS);
		
		$subscriptionSearchResult = $subscriptionDBHandler->searchSubscription("phfeustel@gmx.de", $member);

		$this->AssertEquals($expected, $subscriptionSearchResult);
	}
	
	public function testNewEmailMatch(){
		$subscriptionDBHandler = new SubscriptionDBHandler();
	
		$member =  new Member();
        $member->setNumber(23);
        $member->setFirstname("Philipp");
        $member->setLastname("Feustel");
	
		$subscription = new Subscription();
		$subscription->setId ( 1 );
		$subscription->setEmailaddress ( "phfeustel@gmx.de" );
		$subscription->setNumber ( 23 );
		$subscription->setFirstname ( "Philipp" );
		$subscription->setLastname ( "Feustel" );
        $subscriptions[]=$subscription;

        $expected = new SubscriptionSearchResult();
        $expected->setSubscriptions($subscriptions);
		$expected->setResultCode(SubscriptionSearchResultCode::SUBSCRIPTION_WITH_OTHER_EMAIL_EXISTS);
	
		$subscriptionSearchResult = $subscriptionDBHandler->searchSubscription("phfeustel@host-leaders.de", $member);
	
		$this->assertEquals($expected, $subscriptionSearchResult);
	}
	
	public function testOtherMemberDataMatch(){
		$subscriptionDBHandler = new SubscriptionDBHandler();
	
		$member =  new Member();
        $member->setNumber(23);
        $member->setFirstname("Philipp");
        $member->setLastname("Ferstl");
	
		$subscription = new Subscription();
		$subscription->setId ( 1 );
		$subscription->setEmailaddress ( "phfeustel@gmx.de" );
		$subscription->setNumber ( 23 );
		$subscription->setFirstname ( "Philipp" );
		$subscription->setLastname ( "Feustel" );
        $subscriptions[]=$subscription;

        $expected = new SubscriptionSearchResult();
        $expected->setSubscriptions($subscriptions);
		
		$expected->setResultCode(SubscriptionSearchResultCode::SUBSCRIPTION_WITH_OTHER_MEMBER_DATA_EXISTS);
	
		$subscriptionSearchResult = $subscriptionDBHandler->searchSubscription("phfeustel@gmx.de", $member);
	
		$this->assertEquals($expected, $subscriptionSearchResult);
	}
	
	public function testNoMatch(){
		$subscriptionDBHandler = new SubscriptionDBHandler();
	
		$member =  new Member();
        $member->setNumber(233);
        $member->setFirstname("Philipp");
        $member->setLastname("Feustel");
	
		$expected = new SubscriptionSearchResult();
		$expected->setResultCode(SubscriptionSearchResultCode::SUBSCRIPTION_NOT_EXISTENT);
	
		$subscriptionSearchResult = $subscriptionDBHandler->searchSubscription("phfeustel@host-leaders.de", $member);
	
		$this->assertEquals($expected, $subscriptionSearchResult);
	}
	
	public function testGetSubscriptionList(){
	    $subscriptionDBHandler = new SubscriptionDBHandler();
	    
	    $subscriptions = $subscriptionDBHandler->getSubscriptionList();

	    $this->assertCount(4, $subscriptions);
        $this->assertEquals(4, count($subscriptions));
	}
}

?>