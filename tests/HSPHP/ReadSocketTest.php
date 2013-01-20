<?php

class ReadSocketTest extends PHPUnit_Framework_TestCase
{
	protected $db = 'HSPHP_test';
	
	public function __construct()
	{
		if(file_exists(__DIR__.'/my.cfg'))
		{
			$this->db = trim(file_get_contents(__DIR__.'/my.cfg'));	
		}
	}

    public function testConnection()
	{
		$c = new \HSPHP\ReadSocket();
		$c->connect();
		$this->assertEquals(true,$c->isConnected());
		$c->disconnect();
		$this->assertEquals(false,$c->isConnected());
	}

    public function testIndex()
	{
		$c = new \HSPHP\ReadSocket();
		$c->connect();
		$this->assertEquals(1,$c->getIndexId($this->db,'read1','','key,date,float,varchar,text,set,union,null'));
		$this->assertEquals(1,$c->getIndexId($this->db,'read1','','key,date,float,varchar,text,set,union,null'));
	}

    public function testSelect()
	{
		$c = new \HSPHP\ReadSocket();
		$c->connect();
		$id = $c->getIndexId($this->db,'read1','','key,date,float,varchar,text,set,union,null');
		$c->select($id,'=',array(42));
		$response = $c->readResponse();
		$this->assertEquals(array(array(42,'2010-10-29','3.14159','variable length',"some\r\nbig\r\ntext",'a,c','b',NULL)),$response);
	}

    public function testSelectRange()
	{
		$c = new \HSPHP\ReadSocket();
		$c->connect();
		$id = $c->getIndexId($this->db,'read1','','key');
		$c->select($id,'<=',array(4),3);
		$response = $c->readResponse();
		
		$this->assertEquals(array(array(4),array(3),array(2)),$response);
	}

    public function testSelectMoved()
	{
		$c = new \HSPHP\ReadSocket();
		$c->connect();
		$id = $c->getIndexId($this->db,'read1','','key');
		$c->select($id,'<=',array(4),1,3);
		$response = $c->readResponse();
		
		$this->assertEquals(array(array(1)),$response);
	}

    public function testSelectMovedRange()
	{
		$c = new \HSPHP\ReadSocket();
		$c->connect();
		$id = $c->getIndexId($this->db,'read1','','key');
		$c->select($id,'<=',array(4),2,1);
		$response = $c->readResponse();
		$this->assertEquals(array(array(3),array(2)),$response);
	}
	
	/**
	 * @bug 1
	 */
    public function testSelectWithZeroValue()
	{
		$c = new \HSPHP\ReadSocket();
		$c->connect();
		$id = $c->getIndexId($this->db,'read1','','float');
		$c->select($id,'=',array(100));
		$response = $c->readResponse();
		$this->assertEquals(array(array(0)),$response);
	}

    public function testSelectWithSpecialChars()
    {
        $c = new \HSPHP\ReadSocket();
        $c->connect();
        $id = $c->getIndexId($this->db, 'read1', '', 'text');
        $c->select($id, '=', array(10001));
        $response = $c->readResponse();
        $this->assertEquals(array(array("\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F")), $response);
    }
}
