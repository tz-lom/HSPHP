<?php

namespace HandlerSocket;

class WriteHandler extends ReadHandler
{
	
	/**
	 * @param WriteSocket $io
	 * @param string $db Database name
	 * @param string $table Table name
	 * @param array $keys list of keys
	 * @param string $index name of table key
	 * @param array $fields list of interested fields
	 */
	public function __construct(WriteSocket $io,$db,$table,$keys,$index,$fields)
	{
		parent::__construct($io,$db,$table,$keys,$index,$fields);
	}
	
	/**
	 * Modify rows altering their values
	 *
	 * @param string $compare
	 * @param array $keys
	 * @param array $values
	 *
	 * @return integer How many rows affected
	 */
	public function update($compare,$keys,$values)
	{
		$sk = $this->keys;
		if(is_array($keys))
		{
			foreach($sk as &$value)
			{
				if(!isset($keys[$value])) break;
				$value = $keys[$value];
			}
			array_slice($sk,0,count($keys));
		}
		else
		{
			$sk=array($keys);
		}
		
		
		$sv = $this->fields;
		foreach($sv as &$value)
		{
			if(!isset($values[$value])) break;
			$value = $values[$value];
		}
		array_slice($sv,0,count($values));
		$this->io->update($this->indexId,$compare,$sk,$sv);
		$ret = $this->io->readResponse();
		if($ret instanceof ErrorMessage) throw $ret;
		return $ret[0][0];
	}
	
	/**
	 * Delete rows
	 *
	 * @param string $compare
	 * @param array $keys
	 *
	 * @return integer How many rows affected
	 */
	public function delete($compare,$keys)
	{
		$sk = $this->keys;
		foreach($sk as &$value)
		{
			if(!isset($keys[$value])) break;
			$value = $keys[$value];
		}
		array_slice($sk,0,count($keys));
		$this->io->delete($this->indexId,$compare,$sk);
		$ret = $this->io->readResponse();
		if($ret instanceof ErrorMessage) throw $ret;
		return $ret[0][0];
	}
	
	/**
	 * Insert row into table
	 *
	 * @param array $values
	 * @return bool
	 */
	public function insert($values)
	{
		$sv = $this->fields;
		foreach($sv as &$value)
		{
			if(!isset($values[$value])) break;
			$value = $values[$value];
		}
		array_slice($sv,0,count($values));
		$this->io->insert($this->indexId,$sv);
		$ret = $this->io->readResponse();
		if($ret instanceof ErrorMessage and $ret->getMessage()!='') throw $ret;
		return !($ret instanceof ErrorMessage);
	}
}
