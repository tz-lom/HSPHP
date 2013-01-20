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
 * Class for read commands
 *
 * @package HSPHP
 * @author  Nuzhdin Urii <nuzhdin.urii@gmail.com>
 */

class ReadHandler
{
    /**
     * @var ReadSocket|WriteSocket
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
     * @param ReadCommandsInterface $io
     * @param string                $db     Database name
     * @param string                $table  Table name
     * @param array                 $keys   List of keys
     * @param string                $index  Name of table key
     * @param array                 $fields List of interested fields
     */
    public function __construct(ReadCommandsInterface $io, $db, $table, $keys, $index, $fields)
    {
        $this->db = $db;
        $this->table = $table;
        $this->keys = $keys;
        $this->index = $index;
        $this->fields = $fields;
        $this->io = $io;
        $this->indexId = $this->io->getIndexId($this->db, $this->table, $this->index, $this->fields);
    }


    /**
     * callback for select request
     * @ignore
     */
    public function selectCallback($ret)
    {
        if ($ret instanceof ErrorMessage) {
            return $ret;
        }

        $result = array();
        foreach ($ret as $row) {
            $result[] = array_combine($this->fields, $row);
        }
        return $result;
    }

    /**
     * Selects all rows with key relative as described
     *
     * @param string  $compare
     * @param array   $keys
     * @param integer $limit
     * @param integer $begin
     *
     * @throws ErrorMessage
     *
     * @return array
     */
    public function select($compare, $keys, $limit = 1, $begin = 0)
    {
        $sk = $this->keys;
        if (is_array($keys)) {
            foreach ($sk as &$value) {
                if (!isset($keys[$value])) {
                  break;
                }
                $value = $keys[$value];
            }
            array_slice($sk, 0, count($keys));
        } else {
            $sk = array($keys);
        }
        $this->io->select($this->indexId, $compare, $sk, $limit, $begin);
        $ret = $this->io->registerCallback(array($this, 'selectCallback'));
        if ($ret instanceof ErrorMessage) {
            throw $ret;
        }

        return $ret;
    }
}
