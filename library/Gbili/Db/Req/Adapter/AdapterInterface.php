<?php
namespace Gbili\Db\Req;

use PDO;

/**
 * This is the set of methods that all
 * AbstractReq subclasses
 * are allowed to use to talk to the Db adapter
 * from the AbstractReq::$adapter
 * so AbstractReq::$adapter
 * must implement this interface
 * 
 * @author gui
 *
 */
interface AdapterInterface
{
	public function __construct ( string $dsn, string $username = '', string $password = '', array $driver_options = array());
	public function beginTransaction();
	public function commit();
	public function errorCode();
	public function errorInfo();
	public function exec (string $statement);
	public function getAttribute (int $attribute);
	public function getAvailableDrivers();
	public function lastInsertId(string $name = null);
	public function prepare(string $statement, array $driver_options = array());
	public function query(string $statement);
	public function quote(string $string, int $parameter_type = PDO::PARAM_STR);
	public function rollBack();
	public function setAttribute(int $attribute , mixed $value);
}