<?php

namespace HandlerSocket;

interface WriteCommands extends ReadCommands
{
	public function update($index,$compare,$keys,$values,$limit=1,$begin=0);
	public function delete($index,$compare,$keys,$limit=1,$begin=0);
	public function insert($index,$values);
}
