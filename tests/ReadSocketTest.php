<?php

require('../library/IOException.php');
require('../library/ErrorMessage.php');
require('../library/ReadSocket.php');

		
class ReadSocketTest extends PHPUnit_Framework_TestCase
{
	
	function testConnection()
	{
		$c = new \HandlerSocket\ReadSocket();
		$c->connect();
		$this->assertEquals(true,$c->isConnected());
		$c->disconnect();
		$this->assertEquals(false,$c->isConnected());
	}
	
	function testIndex()
	{
		$c = new \HandlerSocket\ReadSocket();
		$c->connect();
		$this->assertEquals(1,$c->getIndexId('hstest','hstest_table1','','k,v'));
		$this->assertEquals(1,$c->getIndexId('hstest','hstest_table1','','k,v'));
	}
	
	function testSelect()
	{
		$c = new \HandlerSocket\ReadSocket();
		$c->connect();
		$id = $c->getIndexId('hstest','hstest_table1','','k,v');
		$c->select($id,'=',array(1));
		$response = $c->readResponse();
		if($response instanceof \HandlerSocket\ErrorMessage) throw $response;
		$this->assertEquals(array(array(1,'v2')),$response);
	}
	
}
