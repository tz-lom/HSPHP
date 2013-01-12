<?php

namespace HandlerSocket;

interface WriteCommandsInterface
{
    /**
     * Modify rows altering their values
     *
     * @param integer $index
     * @param string  $compare
     * @param array   $keys
     * @param array   $values
     * @param integer $limit
     * @param integer $begin
     */
    public function update($index, $compare, $keys, $values, $limit = 1, $begin = 0);

    /**
     * Perform delete command using compare method for keys
     *
     * @param integer $index
     * @param string  $compare
     * @param array   $keys
     * @param integer $limit
     * @param integer $begin
     *
     * @return integer How many rows affected
     */
    public function delete($index, $compare, $keys, $limit = 1, $begin = 0);

    /**
     * Perform insert command
     *
     * @param integer $index
     * @param array $values
     *
     * @return Boolean
     */
    public function insert($index, $values);
}
