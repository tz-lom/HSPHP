<?php

require_once('../library/IOException.php');
require_once('../library/ErrorMessage.php');
require_once('../library/ReadCommands.php');
require_once('../library/ReadSocket.php');
		
class ReadSocketTest extends PHPUnit_Framework_TestCase
{
	protected $db = 'HSPHP_test';
	
	function __construct()
	{
		if(file_exists('./my.cfg'))
		{
			$this->db = trim(file_get_contents('./my.cfg'));	
		}
	}
	
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
		$this->assertEquals(1,$c->getIndexId($this->db,'read1','','key,date,float,varchar,text,set,union,null'));
		$this->assertEquals(1,$c->getIndexId($this->db,'read1','','key,date,float,varchar,text,set,union,null'));
	}
	
	function testSelect()
	{
		$c = new \HandlerSocket\ReadSocket();
		$c->connect();
		$id = $c->getIndexId($this->db,'read1','','key,date,float,varchar,text,set,union,null');
		$c->select($id,'=',array(42));
		$response = $c->readResponse();
		if($response instanceof \HandlerSocket\ErrorMessage) throw $response;
		$this->assertEquals(array(array(42,'2010-10-29','3.14159','variable length',"some\r\nbig\r\ntext",'a,c','b',NULL)),$response);
	}
	
	function testSelectRange()
	{
		$c = new \HandlerSocket\ReadSocket();
		$c->connect();
		$id = $c->getIndexId($this->db,'read1','','key');
		$c->select($id,'<=',array(4),3);
		$response = $c->readResponse();
		
		$this->assertEquals(array(array(4),array(3),array(2)),$response);
	}
	
	function testSelectMoved()
	{
		$c = new \HandlerSocket\ReadSocket();
		$c->connect();
		$id = $c->getIndexId($this->db,'read1','','key');
		$c->select($id,'<=',array(4),1,3);
		$response = $c->readResponse();
		
		$this->assertEquals(array(array(1)),$response);
	}
	
	function testSelectMovedRange()
	{
		$c = new \HandlerSocket\ReadSocket();
		$c->connect();
		$id = $c->getIndexId($this->db,'read1','','key');
		$c->select($id,'<=',array(4),2,1);
		$response = $c->readResponse();
		$this->assertEquals(array(array(3),array(2)),$response);
	}
	
	/**
	 * @bug 1
	 */
	function testSelectWithZeroValue()
	{
		$c = new \HandlerSocket\ReadSocket();
		$c->connect();
		$id = $c->getIndexId($this->db,'read1','','float');
		$c->select($id,'=',array(100));
		$response = $c->readResponse();
		$this->assertEquals(array(array(0)),$response);
	}
}
