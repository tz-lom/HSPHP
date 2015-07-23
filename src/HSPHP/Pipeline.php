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
 * Class for execute commands
 *
 * @package HSPHP
 * @author  Nuzhdin Urii <nuzhdin.urii@gmail.com>
 */

class Pipeline implements ReadCommandsInterface, WriteCommandsInterface
{
    /**
     * @var array
     */
    protected $queue = array();

    /**
     * @var ReadSocket | WriteSocket
     */
    protected $socket = NULL;

    public function __construct($socket)
    {
        $this->socket = $socket;
        $this->reset();
    }

    public function reset()
    {
        $this->queue = array();
        $this->accumulate = true;
    }

    public function execute()
    {
        foreach ($this->queue as $call) {
            call_user_func_array(array($this->socket, $call['method']), $call['args']);
        }
        $ret = array();
        foreach ($this->queue as $call) {
            $ret[] = call_user_func($call['callback'], $this->socket->readResponse());
        }
        return $ret;
    }

    protected function nullCallback($ret)
    {
        return $ret;
    }

    public function registerCallback($callback = NULL)
    {
        if ($callback === NULL) {
            $callback = $this->nullCallback;
        }

        $this->queue[count($this->queue) - 1]['callback'] = $callback;
        return NULL;
    }

    protected function addToQueue($item)
    {
        $this->queue[] = $item;
    }

    /**
     * {@inheritdoc}
     */
    public function select($index, $compare, $keys, $limit = 1, $begin = 0, $in = array())
    {
        $this->addToQueue(array('method' => 'select', 'args' => func_get_args()));
    }

    /**
     * {@inheritdoc}
     */
    public function update($index, $compare, $keys, $values, $limit = 1, $begin = 0, $in = array())
    {
        $this->addToQueue(array('method' => 'update', 'args' => func_get_args()));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($index, $compare, $keys, $limit = 1, $begin = 0)
    {
        $this->addToQueue(array('method' => 'delete', 'args' => func_get_args()));
    }

    /**
     * {@inheritdoc}
     */
    public function insert($index, $values)
    {
        $this->addToQueue(array('method' => 'insert', 'args' => func_get_args()));
    }

    /**
     * {@inheritdoc}
     */
    public function increment($index, $compare, $keys, $values, $limit = 1, $begin = 0, $in = array())
    {
        $this->addToQueue(array('method' => 'increment', 'args' => func_get_args()));
    }

    /**
     * {@inheritdoc}
     */
    public function decrement($index, $compare, $keys, $values, $limit = 1, $begin = 0, $in = array())
    {
        $this->addToQueue(array('method' => 'decrement', 'args' => func_get_args()));
    }

    /**
     * {@inheritdoc}
     */
    public function openIndex($index, $db, $table, $key, $fields)
    {
        $this->addToQueue(array('method' => 'openIndex', 'args' => func_get_args()));
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexId($db, $table, $key, $fields)
    {
        return $this->socket->getIndexId($db, $table, $key, $fields);
    }
}
