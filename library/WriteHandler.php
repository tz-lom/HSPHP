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
	public function __construct(WriteCommands $io,$db,$table,$keys,$index,$fields)
	{
		parent::__construct($io,$db,$table,$keys,$index,$fields);
	}
	
	/**
	 * callback for update request
	 * @ignore
	 */
	public function update_callback($ret)
	{
		if($ret instanceof ErrorMessage) return $ret;
		return $ret[0][0];
	}
	/**
	 * Modify rows altering their values
	 *
	 * @param string $compare
	 * @param array $keys
	 * @param array $values
	 * @param integer $limit
	 * @param integer $begin
	 *
	 * @return integer How many rows affected
	 */
	public function update($compare,$keys,$values,$limit=1,$begin=0)
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
		$this->io->update($this->indexId,$compare,$sk,$sv,$limit,$begin);
		$ret =$this->io->registerCallback(array($this,'update_callback'));
		if($ret instanceof ErrorMessage) throw $ret;
		return $ret;
	}
	
	/**
	 * callback for delete request
	 * @ignore
	 */
	public function delete_callback($ret)
	{
		if($ret instanceof ErrorMessage) return $ret;
		return $ret[0][0];
	}
	
	/**
	 * Delete rows
	 *
	 * @param string $compare
	 * @param array $keys
	 * @param integer $limit
	 * @param integer $begin
	 * 
	 * @return integer How many rows affected
	 */
	public function delete($compare,$keys,$limit=1,$begin=0)
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
		$this->io->delete($this->indexId,$compare,$sk,$limit,$begin);
		$ret =$this->io->registerCallback(array($this,'delete_callback'));
		if($ret instanceof ErrorMessage) throw $ret;
		return $ret;
	}
	
	
	/**
	 * callback for insert request
	 * @ignore
	 */
	public function insert_callback($ret)
	{
		if($ret instanceof ErrorMessage and $ret->getMessage()!='') return $ret;
		return !($ret instanceof ErrorMessage);
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
		$ret =$this->io->registerCallback(array($this,'insert_callback'));
		if($ret instanceof ErrorMessage) throw $ret;
		return $ret;
	}
}
