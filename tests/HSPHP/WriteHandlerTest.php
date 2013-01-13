<?php

class HSWriteTest extends \HSPHP\WriteHandler
{
	function __construct($io)
	{
		$db = 'HSPHP_test';
		if(file_exists(__DIR__.'/my.cfg'))
		{
			$db = trim(file_get_contents(__DIR__.'/my.cfg'));	
		}
		parent::__construct($io,$db,'write1',array('k'),'',array('k','v'));
	}
}

class WriteHandlerTest extends PHPUnit_Framework_TestCase
{
	function testInsert()
	{
		$io = new \HSPHP\WriteSocket();
		$io->connect();
		$t = new HSWriteTest($io);
		
		$this->assertEquals(0,count($t->select('=',100500)));	// no data with 100500 key
		
		$t->insert(array('k'=>100500,'v'=>'test\nvalue'));
		$this->assertEquals(array(array('k'	=> 100500,
										'v'	=> 'test\nvalue')),$t->select('=',100500));
	}
	
	/**
	 * @depends testInsert
	 */
	function testUpdate()
	{
		$io = new \HSPHP\WriteSocket();
		$io->connect();
		$t = new HSWriteTest($io);
		
		$this->assertEquals(1,$t->update('=',array('k'=>100500),array('k'=>100500,'v'=>42)));
		$this->assertEquals(array(array('k'	=> 100500,
										'v'	=> '42')),$t->select('=',100500));
	}
	
	/**
	 * @depends testUpdate
	 */
	function testDelete()
	{
		$io = new \HSPHP\WriteSocket();
		$io->connect();
		$t = new HSWriteTest($io);
		
		$this->assertEquals(1,$t->delete('=',array('k'=>100500)));
	}
}
