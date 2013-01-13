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
