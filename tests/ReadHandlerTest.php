<?php

require_once('../library/IOException.php');
require_once('../library/ErrorMessage.php');
require_once('../library/ReadCommands.php');
require_once('../library/ReadSocket.php');
require_once('../library/ReadHandler.php');

class HSReadTest extends \HandlerSocket\ReadHandler
{
	function __construct($io)
	{
		$db = 'HSPHP_test';
		if(file_exists('./my.cfg'))
		{
			$db = trim(file_get_contents('./my.cfg'));	
		}
		parent::__construct($io,$db,'read1',array('key'),'',array('key','date','float','varchar','text','set','union','null'));
	}
}

class ReadHandlerTest extends PHPUnit_Framework_TestCase
{
	function testSelect()
	{
		$io = new \HandlerSocket\ReadSocket();
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
