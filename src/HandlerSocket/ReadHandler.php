<?php

namespace HandlerSocket;

class ReadHandler
{
	/**
	 * @var ReadSocket
	 */
	protected $io = NULL;
	/**
	 * @var integer
	 */
	protected $indexId = NULL;
	
	/**
	 * @var array key fields,position matters
	 */
	protected $keys = array();
	/**
	 * @var string key name
	 */
	protected $index = '';
	/**
	 * @var array fields to select
	 */
	protected $fields = array();
	/**
	 * @var string database name
	 */
	protected $db = '';
	/**
	 * @var string table name
	 */
	protected $table = '';
	
	/**
	 * @param ReadSocket $io
	 * @param string $db Database name
	 * @param string $table Table name
	 * @param array $keys list of keys
	 * @param string $index name of table key
	 * @param array $fields list of interested fields
	 */
	public function __construct(ReadCommands $io,$db,$table,$keys,$index,$fields)
	{
		$this->db = $db;
		$this->table = $table;
		$this->keys = $keys;
		$this->index = $index;
		$this->fields = $fields;
		$this->io = $io;
		$this->indexId = $this->io->getIndexId($this->db,$this->table,$this->index,$this->fields);
	}
	
	
	/**
	 * callback for select request
	 * @ignore
	 */
	public function select_callback($ret)
	{
		if($ret instanceof ErrorMessage) return $ret;
		$result = array();
		foreach($ret as $row)
		{
			$result[] = array_combine($this->fields,$row);
		}
		return $result;
	}
	
	/**
	 * Selects all rows with key relative as described
	 *
	 * @param string $compare
	 * @param array $keys
	 * @param integer $limit
	 * @param integer $begin
	 *
	 * @return array
	 */
	public function select($compare,$keys,$limit=1,$begin=0)
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
		$this->io->select($this->indexId,$compare,$sk,$limit,$begin);
		$ret = $this->io->registerCallback(array($this,'select_callback'));
		if($ret instanceof ErrorMessage) throw $ret;
		return $ret;
	}
}
