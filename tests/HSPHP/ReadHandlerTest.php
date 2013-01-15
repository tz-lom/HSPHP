<?php

class HSReadTest extends \HSPHP\ReadHandler
{
	function __construct($io)
	{
		$db = 'HSPHP_test';
		if(file_exists(__DIR__.'/my.cfg'))
		{
			$db = trim(file_get_contents(__DIR__.'/my.cfg'));	
		}
		parent::__construct($io,$db,'read1',array('key'),'',array('key','date','float','varchar','text','set','union','null'));
	}
}

class ReadHandlerTest extends PHPUnit_Framework_TestCase
{
	function testSelect()
	{
		$io = new \HSPHP\ReadSocket();
		$io->connect();
		$t = new HSReadTest($io);
		$this->assertEquals(array(array('key'		=> 42,
										'date'		=> '2010-10-29',
										'float'		=> '3.14159',
										'varchar'	=> 'variable length',
										'text'		=> "some\r\nbig\r\ntext",
										'set'		=> 'a,c',
										'union'		=> 'b',
										'null'		=> NULL)),$t->select('=',42));
	}
}
