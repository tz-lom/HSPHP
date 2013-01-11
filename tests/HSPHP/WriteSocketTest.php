<?php

class WriteSocketTest extends PHPUnit_Framework_TestCase
{
	protected $db = 'HSPHP_test';
	
	function __construct()
	{
		if(file_exists(__DIR__.'/my.cfg'))
		{
			$this->db = trim(file_get_contents(__DIR__.'/my.cfg'));	
		}
	}
	function testInsert()
	{
		$c = new \HandlerSocket\WriteSocket();
		$c->connect('localhost',9999);
		$id = $c->getIndexId($this->db,'write1','','k,v');
		$c->select($id,'=',array(100500));
		$response = $c->readResponse();
		if($response instanceof \HandlerSocket\ErrorMessage) throw $response;
		$this->assertEquals(0,count($response));	// no data with 100500 key
		$c->insert($id,array(100500,'test\nvalue'));
		$response = $c->readResponse();
		if($response instanceof \HandlerSocket\ErrorMessage) throw $response;
		$this->assertEquals(array(),$response);	//return 1 if OK
		
	}
	/**
	 * @depends testInsert
	 */
	function testUpdate()
	{
		$c = new \HandlerSocket\WriteSocket();
		$c->connect('localhost',9999);
		$id = $c->getIndexId($this->db,'write1','','k,v');
		$c->update($id,'=',array(100500),array(100500,42));
		$response = $c->readResponse();
		if($response instanceof \HandlerSocket\ErrorMessage) throw $response;
		$this->assertEquals(array(array(1)),$response);
	}
	
	/**
	 * @depends testUpdate
	 */
	function testDelete()
	{
		$c = new \HandlerSocket\WriteSocket();
		$c->connect('localhost',9999);
		$id = $c->getIndexId($this->db,'write1','','k,v');
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
