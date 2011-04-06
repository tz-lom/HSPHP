<?php

require_once('../library/IOException.php');
require_once('../library/ErrorMessage.php');
require_once('../library/ReadCommands.php');
require_once('../library/WriteCommands.php');
require_once('../library/ReadSocket.php');
require_once('../library/ReadHandler.php');
require_once('../library/WriteSocket.php');
require_once('../library/WriteHandler.php');
require_once('../library/Pipeline.php');

class HSPipelineTest extends \HandlerSocket\WriteHandler
{
	function __construct($io)
	{
		$db = 'HSPHP_test';
		if(file_exists('./my.cfg'))
		{
			$db = trim(file_get_contents('./my.cfg'));	
		}
		parent::__construct($io,$db,'write1',array('k'),'',array('k','v'));
	}
}

class PipelineTest extends PHPUnit_Framework_TestCase
{
	protected $db = 'HSPHP_test';
	
	function __construct()
	{
		if(file_exists('./my.cfg'))
		{
			$this->db = trim(file_get_contents('./my.cfg'));	
		}
	}
	
	function testMultipleSelect()
	{
		$io = new \HandlerSocket\ReadSocket();
		$io->connect();
		
		$pipe = new \HandlerSocket\Pipeline($io);
		
		$accessor = new \HandlerSocket\ReadHandler($pipe,$this->db,'read1',array('key'),'',array('float'));
		
		$accessor->select('=',42);
		$accessor->select('=',12);
		
		$this->assertEquals(array(array(array('float'=>'3.14159')),array(array('float'=>'12345'))),$pipe->execute());
	}
	
	function testBigChain()
	{
		$io = new \HandlerSocket\WriteSocket();
		$io->connect();
		
		$pipe = new \HandlerSocket\Pipeline($io);
		
		$accessor = new \HandlerSocket\WriteHandler($pipe,$this->db,'write1',array('k'),'',array('k','v'));
		
		$accessor->select('=',12);
		$accessor->insert(array('k'=>12,'v'=>'v12'));
		$accessor->select('=',12);
		$accessor->update('=',12,array('k'=>12,'v'=>'u12'));
		$accessor->select('=',12);
		$accessor->delete('=',12);
		$accessor->select('=',12);
		
		$ret = $pipe->execute();
		$this->assertEquals(array(),$ret[0]);
		$this->assertTrue($ret[1]);
		$this->assertEquals(array(array('k'=>'12','v'=>'v12')),$ret[2]);
		$this->assertEquals(1,$ret[3]);
		$this->assertEquals(array(array('k'=>'12','v'=>'u12')),$ret[4]);
		$this->assertEquals(1,$ret[5]);
		$this->assertEquals(array(),$ret[6]);
	}
}
