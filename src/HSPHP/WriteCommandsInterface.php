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
 * Write commands interface
 *
 * @package HSPHP
 * @author  Nuzhdin Urii <nuzhdin.urii@gmail.com>
 */

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
     * @param array $in
     */
    public function update($index, $compare, $keys, $values, $limit = 1, $begin = 0, $in = array());

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

    /**
     * Modify rows incrementing their values
     *
     * @param integer $index
     * @param string  $compare
     * @param array   $keys
     * @param array   $values
     * @param integer $limit
     * @param integer $begin
     * @param array   $in
     */
    public function increment($index, $compare, $keys, $values, $limit = 1, $begin = 0, $in = array());

    /**
     * Modify rows decrementing their values
     *
     * @param integer $index
     * @param string  $compare
     * @param array   $keys
     * @param array   $values
     * @param integer $limit
     * @param integer $begin
     * @param array   $in
     */
    public function decrement($index, $compare, $keys, $values, $limit = 1, $begin = 0, $in = array());
}
