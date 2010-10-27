<?php

require('../library/IOException.php');
require('../library/ErrorMessage.php');
require('../library/ReadSocket.php');
require('../library/WriteSocket.php');
		
class WriteSocketTest extends PHPUnit_Framework_TestCase
{
	function testInsert()
	{
		$c = new \HandlerSocket\WriteSocket();
		$c->connect('localhost',9999);
		$id = $c->getIndexId('hstest','hstest_table1','','k,v');
		$c->select($id,'=',array(100500));
		$response = $c->readResponse();
		if($response instanceof \HandlerSocket\ErrorMessage) throw $response;
		$this->assertEquals(0,count($response));	// no data with 100500 key
		$c->insert($id,array(100500,'test\nvalue'));
		$response = $c->readResponse();
		if($response instanceof \HandlerSocket\ErrorMessage) throw $response;
		$this->assertEquals(array(),$response);	//return 1 if OK
		
	}
	
	function testDelete()
	{
		$c = new \HandlerSocket\WriteSocket();
		$c->connect('localhost',9999);
		$id = $c->getIndexId('hstest','hstest_table1','','k,v');
		$c->delete($id,'=',array(100500));
		$response = $c->readResponse();
		if($response instanceof \HandlerSocket\ErrorMessage) throw $response;
		$this->assertEquals(array(array(1)),$response);	//return 1 if OK
		$c->select($id,'=',array(100500));
		$response = $c->readResponse();
		if($response instanceof \HandlerSocket\ErrorMessage) throw $response;
		$this->assertEquals(0,count($response));	// no data with 100500 key
	}
}
