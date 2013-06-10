<?php
namespace Gbili\Db\Req;

use Gbili\Db\DbInterface,
    PDO;

/**
 * This class is used as a placeholder
 * for the Db adapter plus it forces
 * the adapter to have an interface.
 * 
 * @author gui
 *
 */
abstract class AbstractReq
implements DbInterface
{
	/**
	 * The name of the prefixed adapter
	 * that should be used when no prefiexed
	 * adapter matches the prefix passed
	 * as argument
	 * (i.e. the key under which the adapter instance is stored in )
	 * 
	 * @var unknown_type
	 */
	const FALLBACK_ADAPTER_PREFIX = 'fallbackPrefix';
	
	/**
	 * Contains the adapter instance if not
	 * using the prefixed adapter feature.
	 * Or it contains the adapter instances 
	 * as an array mapping a prefix to the instance
	 * 
	 * @var unknown_type
	 */
	private static $adapter = array();
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $instanceAdapterPointer = null;
	
	/**
	 * Allow to specify a table name
	 * 
	 * @var unknown_type
	 */
	private $tableName = null;
	
	/**
	 * Forces class to have adapter
	 * before instanciation
	 * 
	 * @return unknown_type
	 */
	public function __construct($useDifferentPrefixedAdapter = null)
	{
		if (empty(self::$adapter)) {
			throw new Exception('You must set a Db adapter before instanciating any AbstractReq sublcass.');
		}
		if (null !== $useDifferentPrefixedAdapter) {
			$this->instanceAdapterPointer = self::getPrefixedAdapter($useDifferentPrefixedAdapter);
		}
		$this->getAdapter()->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");
	}
	
	/**
	 * 
	 * @param unknown_type $name
	 * @return unknown_type
	 */
	public function setTableName($name)
	{
		$this->tableName = $name;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getTableName()
	{
		if (false === $this->hasTableName()) {
			throw new Exception("Table name is not set cannot getTableName() untill it is set by setTableName()");
		}
		return $this->tableName;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasTableName()
	{
		return null !== $this->tableName;
	}
	
	/**
	 * @todo ensure adapter is ok
	 * @todo @param AdapterInterface $adapter
	 * @return unknown_type
	 */
	public static function setAdapter($adapter)
	{
		self::setPrefixedAdapter($adapter, self::FALLBACK_ADAPTER_PREFIX);
	}
	
	/**
	 * Prefix must be psr-0
	 * 
	 * @param unknown_type $adapter
	 * @param unknown_type $prefix
	 * @return unknown_type
	 */
	public static function setPrefixedAdapter($adapter, $prefix)
	{
	    //@todo check cast
	    if (null === $adapter) {
	        throw new Exception('adapter is null');
	    }
	    if (!is_string($prefix)) {
	        throw new Exception('Prefix must be a string');
	    }
		self::$adapter[$prefix] = $adapter;
	}
	
	/**
	 * Try to find the closest adapter
	 * 
	 * @param unknown_type $prefix
	 * @return unknown_type
	 */
	public static function getPrefixedAdapter($prefix)
	{
	    echo 'prefixed adpater prefix looks like this:' . $prefix . "\n";
		if (isset(self::$adapter[$prefix])) {
			return self::$adapter[$prefix];
		}
		
		//go to namespace trunk
	    $count = substr_count($prefix, '\\');
        do {
            $rp = strrpos($prefix, '\\');
            $prefix = substr($prefix, 0, $rp);
        } while (--$count > 0 && !isset(self::$adapter[$prefix]));
		
        //adapter found
		if (isset(self::$adapter[$prefix])) {
			return self::$adapter[$prefix]; 
		}
		
		if (!isset(self::$adapter[self::FALLBACK_ADAPTER_PREFIX])) {
		    throw new Exception('There is no adapter for this prefix, neither a fallback adapter. Requested Prefix : ' . $prefix . ', available Prefixes : ' . print_r(self::$adapter, true));
		}
		
		return self::$adapter[self::FALLBACK_ADAPTER_PREFIX];
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public static function getPrefixedAdapters()
	{
		if (!is_array(self::$adapter)) {
			throw new Exception('There are no prefixed adapters');
		}
		return self::$adapter;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Db/Interface#setAdapter($adapter)
	 */
	public function setInstanceAdapter($adapter)
	{
		$this->instanceAdapterPointer = $adapter;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getAdapter()
	{
		if (null === $this->instanceAdapterPointer) {
    		$this->instanceAdapterPointer = self::getPrefixedAdapter(get_class($this));
		}
		return $this->instanceAdapterPointer;
	}
	
	/**
	 * 
	 * @param unknown_type $adapterPrefix
	 * @return unknown_type
	 */
	public function setDifferentPrefixedAdapter($adapterPrefix)
	{
		$this->setInstanceAdapter(self::getPrefixedAdapter($adapterPrefix));
	}
	
	/**
	 * Creates a PDOStatement with the given params
	 * throws up on errors
	 * 
	 * @param unknown_type $sql
	 * @param array $values
	 * @param unknown_type $fetchMode
	 * @return PDOStatement
	 */
	private function prepareAndExecuteStatement($sql, array $values = array())
	{
		if (!is_string($sql)) {
			throw new Exception('You passed a non string argument given : ' . print_r($sql));
		}
		$pdoStmt = $this->getAdapter()->prepare($sql);
		if (false === $pdoStmt) {
			throw new Exception('The database could not successfully prepare the PDOStatement : ' . print_r($this->getAdapter()->errorInfo(), true));
		}
		//bind values if provided
		$res = (!empty($values))? $pdoStmt->execute($values) : $pdoStmt->execute();
		if (false === $res) {
			throw new Exception('The PDOStatement did not execute() successfully : ' . print_r($pdoStmt->errorInfo(), true) . ' SQL : ' . $sql . ' values : ' . print_r($values, true));
		}
		return $pdoStmt;
	}
	
	/**
	 * If errors throws up
	 * otherwise if empty resultset return false
	 * otherwise returns array resultset
	 * 
	 * @param string $sql SQL statement
	 * @param array $values an array mapping each variable key in $sql to a value 
	 * @return false | array
	 */
	public function getResultSet($sql, array $values = array(), $fetchMode = PDO::FETCH_ASSOC)
	{
		$pdoStmt = $this->prepareAndExecuteStatement($sql, $values);
		if ($pdoStmt->rowCount() === 0) {
			return false;
		}
		return $pdoStmt->fetchAll($fetchMode);
	}
	
	/**
	 * Inserts the data and returns the lasInsertId
	 * @param unknown_type $sql
	 * @param array $values
	 * @return PDOStatement
	 */
	public function insertUpdateData($sql, array $values = array())
	{
		$pdoStmt = $this->prepareAndExecuteStatement($sql, $values);
		//always close cursor to allow other queries
		$pdoStmt->closeCursor();
		return true;
	}
	
	/**
	 * If the user wants to get the id if the elment exists, then
	 * he must specify the $idColumnName and set $returnId = true
	 * 
	 * @param $sql
	 * @param $values
	 * @param $idColumnName
	 * @param $returnId
	 * @return unknown_type
	 */
	public function existsElement($sql, array $values, $idColumnName = null, $returnId = false)
	{
		$res = $this->getResultSet($sql, $values);
		if (false === $returnId) {
			//the user wants only a boolean result
			return (boolean) $res;
		}
		//arrived here then the user wants to get the id or false
		if (null === $idColumnName) {
			throw new Exception('You must specify the $idColumnName parameter, if you want to be able to get the id in existsElement()');
		}
		//return the id if the res is an array or return false
		return (is_array($res))? $res[0][(string) $idColumnName] : false;
	}

}