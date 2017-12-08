<?php
require '../../vendor/autoload.php';

use cds\Database\DBHandler;

class DBHandlerTest extends PHPUnit_Framework_TestCase{

	public function testConnect()
	{
		// Arrange
		$dbHandler = new DBHandler();
	
		$dbHandler->connect();
		
		$this->assertNotNull($dbHandler->getMysqli());
		
		$this->assertNotNull($dbHandler->getMysqli()->get_server_info());
	}	
}

?>