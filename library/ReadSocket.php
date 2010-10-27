<?php

namespace HandlerSocket;

class ReadSocket
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
		$encoded = '';
		$len = strlen($string);
		for($t=0;$t<$len;$t++)
		{
			if(ord($string[$t])>0x0f)
				$encoded.=$string[$t];
			else
				$encoded.=self::ESC.chr(ord($string[$t])+self::ESC_SHIFT);
		}
		return $encoded;
	}
	
	/**
	 * Decode string from server
	 *
	 * @param string $encoded
	 * @return string
	 */
	protected function decodeString($encoded)
	{
		$string = '';
		$len = strlen($encoded);
		for($t=0;$t<$len;$t++)
		{
			if(ord($encoded[$t])!=self::ESC)
				$string.=$encoded[$t];
			else
				$string.=chr(ord($encoded[++$t])-self::ESC_SHIFT);
		}
		return $string;
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
			return new \HandlerSocket\ErrorMessage($vals[2],$vals[0]);
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
		if($limit>1)
		{
			if($begin>0)
				$query.=self::SEP.$limit.self::SEP.$begin;
			else
				$query.=self::SEP.$limit;
		}
		$this->sendStr($query.self::EOL);
	}
	
}
