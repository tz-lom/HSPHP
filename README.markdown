Master: [![Build Status](https://api.travis-ci.org/tz-lom/HSPHP.png?branch=master)](http://travis-ci.org/tz-lom/HSPHP)

# HandlerSocker Library for PHP

This library provides an API for communicating with the HandlerSocket plugin for
MySQL compatible databases(MySQL, MariaDB, Percona).

For more information on HandlerSocket, check out the 
[MariaDB Documentation on HandlerSocket here](https://mariadb.com/kb/en/handlersocket/).

## Installation

[Once you have composer installed](https://getcomposer.org/doc/00-intro.md#system-requirements "Getting Started With Composer"),
run the following in your php project directory:

        php composer.phar require tz-lom/hsphp --no-update

# Usage Examples

## Select

```php
$c = new \HSPHP\ReadSocket();
$c->connect();
$id = $c->getIndexId('data_base_name', 'table_name', '', 'id,name,some,thing,more');
$c->select($id, '=', array(42)); // SELECT WITH PRIMARY KEY
$response = $c->readResponse();

//SELECT with IN statement
$c = new \HSPHP\ReadSocket();
$c->connect();
$id = $c->getIndexId('data_base_name', 'table_name', '', 'id,name,some,thing,more');
$c->select($id, '=', array(0), 0, 0, array(1,42,3));
$response = $c->readResponse();
```

## Update

```php
$c = new \HSPHP\WriteSocket();
$c->connect('localhost',9999);
$id = $c->getIndexId('data_base_name','table_name','','k,v');
$c->update($id,'=',array(100500),array(100500,42)); // Update row(k,v) with id 100500 to  k = 100500, v = 42
$response = $c->readResponse(); // Has 1 if OK
```

## Batch update

```php
$c = new \HSPHP\WriteSocket();
$c->connect('localhost',9999);
$id = $c->getIndexId('data_base_name','table_name','','k,v');
$c->update($id,'=',array(100500),array(100500,42), 2, 0, array(100501, 100502); // Update rows where k IN (100501, 100502)
$response = $c->readResponse(); // Has 1 if OK
```

## Delete

```php
$c = new \HSPHP\WriteSocket();
$c->connect('localhost',9999);
$id = $c->getIndexId('data_base_name','table_name','','k,v');
$c->delete($id,'=',array(100500));
$response = $c->readResponse(); //return 1 if OK
```

## Insert

```php
$c = new \HSPHP\WriteSocket();
$c->connect('localhost',9999);
$id = $c->getIndexId('data_base_name','table_name','','k,v');
$c->insert($id,array(100500,'test\nvalue'));
$response = $c->readResponse(); //return array() if OK
```

## Increment

```php
$c = new \HSPHP\WriteSocket();
$c->connect('localhost',9999);
$id = $c->getIndexId('data_base_name','table_name','','v');
$c->increment($id,'=',array(100500),array(2)); // Increment v column by 2
$response = $c->readResponse(); // Has 1 if OK
```

## Increment

```php
$c = new \HSPHP\WriteSocket();
$c->connect('localhost',9999);
$id = $c->getIndexId('data_base_name','table_name','','v');
$c->decrement($id,'=',array(100500),array(2)); // Decrement v column by 2
$response = $c->readResponse(); // Has 1 if OK
```
