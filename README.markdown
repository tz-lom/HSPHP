Master: [![Build Status](https://api.travis-ci.org/tz-lom/HSPHP.png?branch=master)](http://travis-ci.org/tz-lom/HSPHP)

# HandlerSocker Library for PHP

This library provide API for communicating with HandlerSocket plugin for MySQL.

## Installation

You just need to get composer and than:

        php composer.phar require tz-lom/hsphp --no-update

## Examples

# Select

        $c = new \HSPHP\ReadSocket();
        $c->connect();
        $id = $c->getIndexId('data_base_name', 'table_name', '', 'id,name,some,thing,more');
        $c->select($id, '=', array(42)); // SELECT WITH PRIMARY KEY
        $response = $c->readResponse();

# Update

		$c = new \HSPHP\WriteSocket();
		$c->connect('localhost',9999);
		$id = $c->getIndexId('data_base_name','table_name','','k,v');
		$c->update($id,'=',array(100500),array(100500,42)); // Update row(k,v) with id 100500 to  k = 100500, v = 42
		$response = $c->readResponse(); // Has 1 if OK

# Delete

		$c = new \HSPHP\WriteSocket();
		$c->connect('localhost',9999);
		$id = $c->getIndexId('data_base_name','table_name','','k,v');
		$c->delete($id,'=',array(100500));
		$response = $c->readResponse(); //return 1 if OK

# Insert

		$c = new \HSPHP\WriteSocket();
		$c->connect('localhost',9999);
		$id = $c->getIndexId('data_base_name','table_name','','k,v');
		$c->insert($id,array(100500,'test\nvalue'));
		$response = $c->readResponse(); //return array() if OK

