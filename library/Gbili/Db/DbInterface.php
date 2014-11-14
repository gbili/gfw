<?php
namespace Gbili\Db;

use PDO;

/**
 * This interface is usefull for the registry
 * 
 * @author gui
 *
 */
interface DbInterface
{
	/**
	 * 
	 * @return unknown_type
	 */
	public function __construct();
	
	/**
	 * 
	 * @param unknown_type $adapter
	 * @return unknown_type
	 */
	public static function setAdapter($adapter);
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getAdapter();

	/**
	 * Returns a resultset or boolean
	 * 
	 * @param unknown_type $sql
	 * @param array $values
	 * @param unknown_type $fetchMode
	 * @return unknown_type
	 */
	public function getResultSet($sql, array $values = array(), $fetchMode = PDO::FETCH_ASSOC);
}