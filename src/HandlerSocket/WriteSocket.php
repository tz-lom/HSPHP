<?php

namespace HandlerSocket;

class WriteSocket extends ReadSocket implements WriteCommandsInterface
{

    /**
     * Connect to Handler Socket
     *
     * @param string  $server
     * @param integer $port
     *
     * @throws IOException
     */
    public function connect($server = 'localhost', $port = 9999)
    {
        parent::connect($server, $port);
    }

    /**
     * {@inheritdoc}
     */
    public function update($index, $compare, $keys, $values, $limit = 1, $begin = 0)
    {
        $query = $index . self::SEP . $compare . self::SEP . count($keys);
        foreach ($keys as $key) {
            $query .= self::SEP . $this->encodeString((string)$key);
        }
        $query .= self::SEP . $limit . self::SEP . $begin;
        $query .= self::SEP . 'U';
        foreach ($values as $key) {
            $query .= self::SEP . $this->encodeString((string)$key);
        }
        $this->sendStr($query . self::EOL);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($index, $compare, $keys, $limit = 1, $begin = 0)
    {
        $query = $index . self::SEP . $compare . self::SEP . count($keys);
        foreach ($keys as $key) {
            $query .= self::SEP . $this->encodeString((string)$key);
        }
        $query .= self::SEP . $limit . self::SEP . $begin;
        $query .= self::SEP . 'D';
        $this->sendStr($query . self::EOL);
    }

    /**
     * {@inheritdoc}
     */
    public function insert($index, $values)
    {
        $query = $index . self::SEP . '+' . self::SEP . count($values);
        foreach ($values as $key) {
            $query .= self::SEP . $this->encodeString((string)$key);
        }
        $this->sendStr($query . self::EOL);
    }
}
