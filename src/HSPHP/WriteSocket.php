<?php

/*
 * This file is part of HSPHP.
 *
 * (c) Nuzhdin Urii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HSPHP;

/**
 * Class for Write Socket
 *
 * @package HSPHP
 * @author  Nuzhdin Urii <nuzhdin.urii@gmail.com>
 */

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
    public function update($index, $compare, $keys, $values, $limit = 1, $begin = 0, $in = array())
    {
        $this->genericUpdate($index, 'U', $compare, $keys, $values, $limit, $begin, $in);
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

    /**
     * {@inheritdoc}
     */
    public function increment($index, $compare, $keys, $values, $limit = 1, $begin = 0, $in = array())
    {
        $this->genericUpdate($index, '+', $compare, $keys, $values, $limit, $begin, $in);
    }

    /**
     * {@inheritdoc}
     */
    public function decrement($index, $compare, $keys, $values, $limit = 1, $begin = 0, $in = array())
    {
        $this->genericUpdate($index, '-', $compare, $keys, $values, $limit, $begin, $in);
    }

    /**
     * genericUpdate method (used for updates, increment and decrements)
     *
     * @param integer $index
     * @param staing  $operation
     * @param string  $compare
     * @param array   $keys
     * @param array   $values
     * @param integer $limit
     * @param integer $begin
     * @param array $in
     */
    private function genericUpdate($index, $operation, $compare, $keys, $values, $limit = 1, $begin = 0, $in = array())
    {
        $query = $index . self::SEP . $compare . self::SEP . count($keys);
        foreach ($keys as $key) {
            $query .= self::SEP . $this->encodeString((string)$key);
        }
        $query .= self::SEP . $limit . self::SEP . $begin;

        if ($in) {
            $query .= self::SEP . '@' . self::SEP . '0' . self::SEP . count($in);
            foreach($in as $inValue) {
                $query .= self::SEP . $this->encodeString((string)$inValue);
            }
        }

        $query .= self::SEP . $operation;
        foreach ($values as $key) {
            $query .= self::SEP . $this->encodeString((string)$key);
        }
        $this->sendStr($query . self::EOL);
    }
}
