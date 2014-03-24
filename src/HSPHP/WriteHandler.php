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
 * Class for Write Handler
 *
 * @package HSPHP
 * @author  Nuzhdin Urii <nuzhdin.urii@gmail.com>
 */

class WriteHandler extends ReadHandler
{
    /**
     * @param WriteCommandsInterface $io
     * @param string                 $db     Database name
     * @param string                 $table  Table name
     * @param array                  $keys   List of keys
     * @param string                 $index  Name of table key
     * @param array                  $fields List of interested fields
     */
    public function __construct(WriteCommandsInterface $io, $db, $table, $keys, $index, $fields)
    {
        parent::__construct($io, $db, $table, $keys, $index, $fields);
    }

    /**
     * callback for update request
     * @ignore
     */
    public function updateCallback($ret)
    {
        if ($ret instanceof ErrorMessage) {
            return $ret;
        }

        return $ret[0][0];
    }

    /**
     * Modify rows altering their values
     *
     * @param string  $compare
     * @param array   $keys
     * @param array   $values
     * @param integer $limit
     * @param integer $begin
     *
     * @throws ErrorMessage
     *
     * @return integer
     */
    public function update($compare, $keys, $values, $limit = 1, $begin = 0)
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


        $sv = $this->fields;
        foreach ($sv as &$value) {
            if (!isset($values[$value])) {
                break;
            }

            $value = $values[$value];
        }
        array_slice($sv, 0, count($values));
        $this->io->update($this->indexId, $compare, $sk, $sv, $limit, $begin);
        $ret = $this->io->registerCallback(array($this, 'updateCallback'));
        if ($ret instanceof ErrorMessage) {
            throw $ret;
        }

        return $ret;
    }

    /**
     * callback for delete request
     * @ignore
     */
    public function deleteCallback($ret)
    {
        if ($ret instanceof ErrorMessage) {
            return $ret;
        }

        return $ret[0][0];
    }

    /**
     * Delete Rows
     *
     * @param string  $compare
     * @param array   $keys
     * @param integer $limit
     * @param integer $begin
     *
     * @throws ErrorMessage
     *
     * @return integer How many rows affected
     */
    public function delete($compare, $keys, $limit = 1, $begin = 0)
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
        $this->io->delete($this->indexId, $compare, $sk, $limit, $begin);
        $ret = $this->io->registerCallback(array($this, 'deleteCallback'));
        if ($ret instanceof ErrorMessage) {
            throw $ret;
        }

        return $ret;
    }


    /**
     * callback for insert request
     * @ignore
     */
    public function insertCallback($ret)
    {
        if ($ret instanceof ErrorMessage and $ret->getMessage() != '') {
            return $ret;
        }

        return !($ret instanceof ErrorMessage);
    }

    /**
     * Insert Rows
     *
     * @param array $values
     *
     * @throws ErrorMessage
     *
     * @return Boolean
     */
    public function insert($values)
    {
        $sv = $this->fields;
        foreach ($sv as &$value) {
            if (!isset($values[$value])) {
                break;
            }

            $value = $values[$value];
        }
        array_slice($sv, 0, count($values));
        $this->io->insert($this->indexId, $sv);
        $ret = $this->io->registerCallback(array($this, 'insertCallback'));
        if ($ret instanceof ErrorMessage) {
            throw $ret;
        }

        return $ret;
    }
}
