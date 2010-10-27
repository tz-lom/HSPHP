<?php

namespace HandlerSocket;

class WriteSocket extends ReadSocket
{
	
	/**
	 * Connect to Handler Socket
	 *
	 * @param string $server
	 * @param integer $port
	 * @throws \HandlerSocket\IOException
	 */
	public function connect($server='localhost',$port=9999)
	{
		parent::connect($server,$port);
	}

	/**
	 * preforme update command using compare method for keys
	 *
	 * @param integer $indexes
	 * @param string $compare
	 * @param array $keys
	 * @param array $values
	 * @param integer $limit
	 * @param integer $begin
	 */
	public function update($index,$compare,$keys,$values,$limit=1,$begin=0)
	{
		$query = $index.self::SEP.$compare.self::SEP.count($keys);
		foreach($keys as $key)
		{
			$query.=self::SEP.$this->encodeString((string)$key);
		}
		$query.=self::SEP.$limit.self::SEP.$begin;
		$query.=self::SEP.'U';
		foreach($values as $key)
		{
			$query.=self::SEP.$this->encodeString((string)$key);
		}
		$this->sendStr($query.self::EOL);
	}
	
	/**
	 * performe delete command using compare method for keys
	 *
	 * @param integer $index
	 * @param string $compare
	 * @param array $keys
	 * @param integer $limit
	 * @param integer $begin
	 */
	public function delete($index,$compare,$keys,$limit=1,$begin=0)
	{
		$query = $index.self::SEP.$compare.self::SEP.count($keys);
		foreach($keys as $key)
		{
			$query.=self::SEP.$this->encodeString((string)$key);
		}
		$query.=self::SEP.$limit.self::SEP.$begin;
		$query.=self::SEP.'D';
		$this->sendStr($query.self::EOL);
	}
	
	/**
	 * perform insert command
	 *
	 * @param integer $index
	 * @param array $values
	 */
	public function insert($index,$values)
	{
		$query = $index.self::SEP.'+'.self::SEP.count($values);
		foreach($values as $key)
		{
			$query.=self::SEP.$this->encodeString((string)$key);
		}
		$this->sendStr($query.self::EOL);
	}
}
