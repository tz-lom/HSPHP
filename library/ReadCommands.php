<?php

namespace HandlerSocket;

interface ReadCommands
{
	public function openIndex($index,$db,$table,$key,$fields);
	public function getIndexId($db,$table,$key,$fields);
	public function select($index,$compare,$keys,$limit=1,$begin=0);
}
