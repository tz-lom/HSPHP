<?php

namespace HandlerSocket;

interface ReadCommands
{
	public function openIndex($index,$db,$table,$key,$fields);
	public function getIndexId($db,$table,$key,$fields);
	public function select($index,$compare,$keys,$limit=1,$begin=0);
}

class ReadSocket implements ReadCommands
{
	const EOL = "\n";
	const SEP = "\t";
	const NULL = "\0";
	const ESC = "\1";
	const ESC_SHIFT = 0x40;
	
	protected $socket = NULL;
	/**
	 * @var array
	 */
	protected $indexes = array();
	/**
	 * @var iteger
	 */
	protected $currindex = 1;
	
	/**
	 * Connect to Handler Socket
	 *
	 * @param string $server
	 * @param integer $port
	 * @throws \HandlerSocket\IOException
	 */
	public function connect($server='localhost',$port=9998)
	{
		$addr = "tcp://$server:$port";
		$this->socket = stream_socket_client($addr,$errc,$errs,STREAM_CLIENT_CONNECT);
		if(!$this->socket)
			throw new \HandlerSocket\IOException("Connection to $server:$port failed");
	}
	
	public function __destruct()
	{
		$this->disconnect();
	}
	
	/**
	 * Disconnect from server
	 */
	public function disconnect()
	{
		@fclose($this->socket);
		$this->socket = NULL;
		$this->indexes = array();
		$this->currindex = 1;
	}
	
	/**
	 * Is connected
	 *
	 * @return bool
	 */
	public function isConnected()
	{
		return is_resource($this->socket);
	}
	
	/**
	 * Receive one string from server,string havn't trailing \n
	 *
	 * @param bool $read Specifies socket
	 * @throws \HandlerSocket\IOException
	 * @return string
	 */
	protected function recvStr($read = true)
	{
		$str = @fgets($this->socket);
		if(!$str)
		{
			$this->disconnect();
			throw new \HandlerSocket\IOException('Cannot read from socket');
		}
		return substr($str,0,-1);
	}
	
	/**
	 * Send command to server
	 *
	 * @throws \HandlerSocket\IOException
	 * @param string $string
	 */
	protected function sendStr($string)
	{
		if(!$this->isConnected()) throw new \HandlerSocket\IOException('No active connection');
		$string = (string)$string;                                                                   
		while ($string)
		{                                                                                           
			$bytes = @fwrite($this->socket, $string);                                                              
			if($bytes === false)
			{
				$this->disconnect();
				throw new \HandlerSocket\IOException('Cannot write to socket');
			}
			if ($bytes == 0)
			{
				return;
			}
			$string = substr($string, $bytes);                                                                      
		}
	}
	
	/**
	 * Encode string for sending to server
	 *
	 * @param string $string
	 * @return string
	 */
	protected function encodeString($string)
	{
		if(is_null($string))
		{
			return "\0";
		}
		else
		{
            return strtr($string,
						 array(	"\x00" => "\x01\x40",
								"\x01" => "\x01\x41",
								"\x02" => "\x01\x42",
								"\x03" => "\x01\x43",
								"\x04" => "\x01\x44",
								"\x05" => "\x01\x45",
								"\x06" => "\x01\x46",
								"\x07" => "\x01\x47",
								"\x08" => "\x01\x48",
								"\x09" => "\x01\x49",
								"\x0A" => "\x01\x4A",
								"\x0B" => "\x01\x4B",
								"\x0C" => "\x01\x4C",
								"\x0D" => "\x01\x4D",
								"\x0E" => "\x01\x4E",
								"\x0F" => "\x01\x4F"));
        }
	}
	
	/**
	 * Decode string from server
	 *
	 * @param string $encoded
	 * @return string
	 */
	protected function decodeString($encoded)
	{
		if($encoded === "\0")
		{
            return NULL;
		}
        else
		{
            return strtr($encoded,
						 array(	"\x01\x40" => "\x00",
								"\x01\x41" => "\x01",
								"\x01\x42" => "\x02",
								"\x01\x43" => "\x03",
								"\x01\x44" => "\x04",
								"\x01\x45" => "\x05",
								"\x01\x46" => "\x06",
								"\x01\x47" => "\x07",
								"\x01\x48" => "\x08",
								"\x01\x49" => "\x09",
								"\x01\x4A" => "\x0A",
								"\x01\x4B" => "\x0B",
								"\x01\x4C" => "\x0C",
								"\x01\x4D" => "\x0D",
								"\x01\x4E" => "\x0E",
								"\x01\x4F" => "\x0F"));
		}
	}
	
	/**
	 * Read response from server
	 *
	 * @return \HandlerSocket\ErrorMessage
	 * @return array
	 */
	public function readResponse()
	{
		$response = $this->recvStr();
		$vals = explode(self::SEP,$response);
		if($vals[0]!=0)
		{
			//error occured
			return new \HandlerSocket\ErrorMessage(isset($vals[2])?$vals[2]:'',$vals[0]);
		}
		else
		{
			$numcols = intval($vals[1]);
			$result = array();
			reset($vals);
			next($vals);
			$group = array();
			$readed = $numcols;
			while($item = next($vals))
			{
				$group[] = $this->decodeString($item);
				if(--$readed==0)
				{
					$result[] = $group;
					$group = array();
					$readed = $numcols;
				}
			}
			return $result;
		}
	}
	
	/**
	 * Perform opening index $index over $key of table $db.$table and prepairing read $fields
	 *
	 * @param integer $index
	 * @param string $db
	 * @param string $table
	 * @param string $key
	 * @param string $fields
	 */
	public function openIndex($index,$db,$table,$key,$fields)
	{
		if(empty($key))$key='PRIMARY';
		$this->sendStr(implode(self::SEP,array('P',
												intval($index),
												$this->encodeString($db),
												$this->encodeString($table),
												$this->encodeString($key),
												$this->encodeString($fields))).self::EOL);
	}
	
	/**
	 * Register index Id in socket and return it,caches indexes for future use
	 *
	 * @param string $db
	 * @param string $table
	 * @param string $key
	 * @param string $fields
	 *
	 * @throws \HandlerSocket\ErrorMessage
	 * 
	 * @return integer
	 */
	public function getIndexId($db,$table,$key,$fields)
	{
		if(is_array($fields)) $fields = implode(',',$fields);
		if(isset($this->indexes[$db][$table][$key][$fields]))
			return $this->indexes[$db][$table][$key][$fields];
		else
		{
			//register new index ,save it and return		
			$this->openIndex($this->currindex,$db,$table,$key,$fields);
			$ret = $this->readResponse();
			if(!$ret instanceof \HandlerSocket\ErrorMessage)
			{
				$this->indexes[$db][$table][$key][$fields] = $this->currindex++;
				return $this->currindex-1;
			}
			else
				throw $ret;
		}
	}
	
	/**
	 * performe select command using compare method for keys
	 *
	 * @param integer $index
	 * @param string $compare
	 * @param array $keys
	 * @param integer $limit
	 * @param integer $begin
	 */
	public function select($index,$compare,$keys,$limit=1,$begin=0)
	{
		$query = $index.self::SEP.$compare.self::SEP.count($keys);
		foreach($keys as $key)
		{
			$query.=self::SEP.$this->encodeString((string)$key);
		}
		if($begin>0)
			$query.=self::SEP.($begin+$limit).self::SEP.$begin;
		else
		{
			if($limit>1)
				$query.=self::SEP.$limit;
		}
		$this->sendStr($query.self::EOL);
	}
	
	/**
	 * Register callback that must process response from server
	 *   very useful for cache/pipeline system
	 *
	 * @param callback $callback
	 */
	public function registerCallback($callback)
	{
		return call_user_func($callback,$this->readResponse());
	}
	
}
