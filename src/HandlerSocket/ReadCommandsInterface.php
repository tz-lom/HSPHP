<?php

namespace HandlerSocket;

interface ReadCommandsInterface
{
    /**
     * Perform opening index $index over $key of table $db.$table and prepairing read $fields
     *
     * @param integer $index
     * @param string  $db
     * @param string  $table
     * @param string  $key
     * @param string  $fields
     *
     * @return void
     */
    public function openIndex($index, $db, $table, $key, $fields);

    /**
     * Register index Id in socket and return it,caches indexes for future use
     *
     * @param string $db
     * @param string $table
     * @param string $key
     * @param string $fields
     *
     * @return integer
     */
    public function getIndexId($db, $table, $key, $fields);

    /**
     * Perform select command using compare method for keys
     *
     * @param integer $index
     * @param string  $compare
     * @param array   $keys
     * @param integer $limit
     * @param integer $begin
     *
     * @return void
     */
    public function select($index, $compare, $keys, $limit = 1, $begin = 0);
}
