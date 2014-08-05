<?php
namespace Gbili\Db\Req;

/**
 * This class is used as a placeholder
 * for the Db adapter plus it forces
 * the adapter to have an interface.
 * 
 * @author gui
 *
 */
abstract class AbstractReq
implements \Gbili\Db\DbInterface
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
     * Array of token to value to be used
     * as parameters
     * @var array
     */
    protected $parameters = array();

    /**
     * Avoid parameter names collisions
     *
     */
    protected $canonicalParameterNamesInUse=array();

    /**
     * Sql being executed
     * @var string
     */
    protected $sql;

    /**
     * In a SELECT t.field AS key FROM ...
     * array('key' => 't.field')
     * They allow to use the keys instead of
     * the fields when building the criteria
     * array.
     */
    protected $keyedFields = array();
	
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

        $this->setKeyedFields($this->loadKeyedFields());
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
     * Create an adapter from an options array
     */
    public static function generateAdapter(array $options)
    {
        if (isset($options['db_req'])) {
            $options = $options['db_req'];
        }
        $missingKeys = array_diff(array('driver_class', 'driver_options', 'dsn', 'driver_params'), array_keys($options));
        if (!empty($missingKeys)) {
            throw new Exception('Missing options keys : ' . print_r($missingKeys, true));
        }

        $driverClass   = $options['driver_class'];
        $driverOptions = $options['driver_options'];
        $dsn           = $options['dsn'];
        $driverParams  = $options['driver_params'];

        $missingKeys = array_diff(array('username', 'password'), array_keys($driverParams));
        if (!empty($missingKeys)) {
            throw new Exception('Missing options keys : ' . print_r($missingKeys, true));
        }
        $username      = $driverParams['username'];
        $password      = $driverParams['password'];

        $instance      = new $driverClass($dsn, $username, $password, $driverOptions);
        
        if (isset($options['adapter_prefix'])) {
            self::setPrefixedAdapter($instance, $options['adapter_prefix']);
        } else {
            self::setAdapter($instance);
        }
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
	public function getResultSet($sql, array $values = array(), $fetchMode = \PDO::FETCH_ASSOC)
	{
        $this->setSql($sql);

        if (empty($values) && $this->hasParameters()) {
            $values = $this->getParameters();
        }
		$pdoStmt = $this->prepareAndExecuteStatement($sql, $values);
		return $pdoStmt->fetchAll($fetchMode);
	}

    /**
     * Adds a where clause if there is a critera array and returns
     * the result
     *
     * @TODO this is messed up, the resultset should be an object that holds records
     * and the sql and the parameters, they should defeinetely not be kept here since
     * it messes up the query for the next getResultSet call (because paramters are not
     * removed after the query)
     *
     * @param string $baseSql sql string without the WITH clause
     * @param array $criteria critera array to be converted to sql string
     * @see criteriaToString
     */
	protected function getResultSetByCriteria($baseSql, array $criteria = array(), $trailingSql = '')
	{
        $sqlParts = array();
        $sqlParts[] = $baseSql;
        $sqlParts[] = $this->where($criteria);
        $sqlParts[] = $this->groupBy();
        $sqlParts[] = $trailingSql;

        $sql = implode(' ', $sqlParts);
        return $this->getResultSet($sql, $this->getParameters());
	}

    /**
     * Group by clause for all the keyed fields
     * according to SQL standard?
     * @return string
     */
    protected function groupBy()
    {
        $groupableFields = array_filter($this->getKeyedFields(), function ($element) {
            // Disable aggregate functions
            return (0 < preg_match('/[^|()]/', $element));
        });
        die(var_dump($groupableFields));
        return 'GROUP BY ' . implode(', ', $groupableFields);
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

    /**
     * Where clause composed from critera 
     * @param $criteria array
     * @see self::criteriaToString() for $criteria param
     * @return string, empty string if empty criteria
     */
    public function where($criteria)
    {
        if (empty($criteria)) {
            return '';
        }
        $criteriaSql = $this->criteriaToString($criteria);
        return "WHERE $criteriaSql";
    }

    /**
     * Converts a criteria array to an SQL criteria string
     * @param array $criteria array(
     *                            'or' => array(
     *                                 $fieldKey1 => array('=' => $value1),
     *                                 $fieldKey2 => array('like' => $value2),
     *                                 'and' => array(
     *                                      $fieldKey3 => array('>=' => $value3),
     *                                      $fieldKey4 => array('<=' => $value4),
     *                                 ),
     *                            ),
     *                        );
     * The criteria array would be converted to a string:
     *     ( $fieldKey1 = $value1 or $fieldKey2 like $value2 or ($fieldKey3 >= $value3 and $fieldKey4 <= $value4 )
     */
    public function criteriaToString(array $criteria, array $keyToField=array())
    {
        if (empty($keyToField)) {
            $keyToField = $this->getKeyedFields();
        }
        $operator = null;
        if (isset($criteria['and'])) {
            $operator = 'and';
        }
        if (isset($criteria['or'])) {
            if (null !== $operator) {
                throw new \Exception('One boolean comparison operator per criteria array level');
            }
            $operator = 'or';
        }
        if (null === $operator) {
            if (1 < count($criteria)) {
                throw new \Exception('If there are more than one operations, there must be a boolan operator');
            }
            return $this->conditionToString($keyToField, current($criteria));
        }

        $conditions = array();
        $conditionGroup = $criteria[$operator];
        foreach ($conditionGroup as $k => $condition) {
            if (is_string($k) && is_array($condition)) {
                $subOperator = $k;
                $subConditionGroup = $condition;
                $conditions[] = $this->criteriaToString(array($subOperator => $subConditionGroup), $keyToField);
                continue;
            }
            $conditions[] = $this->conditionToString($keyToField, $condition);
        }

        if (1 === count($conditions)) {
            return current($conditions);
        }
        return '( ' . implode( ' ' . $operator . ' ', $conditions) . ' )';
    }

    public function conditionToString(array $keyToField, array $condition)
    {
        $fieldKey = key($condition); 
        if (!isset($keyToField[$fieldKey])) {
            throw new \Exception("The field key: $fieldKey, specified in operation, is not set in \$keyToField : " . print_r($keyToField, true) . ' condition : ' . print_r($condition, true));
        }
        $field     = $keyToField[$fieldKey];
        $condition = current($condition);
        $operator  = key($condition);
        $operand   = $condition[$operator];

        $parameterIdentifier = $this->addParameter(":$fieldKey", $operand);

        return "$field $operator $parameterIdentifier";
    }

    public function addParameter($name, $value)
    {
        if (!isset($this->canonicalParameterNamesInUse[$name])) {
            $this->canonicalParameterNamesInUse[$name] = 0;
            $this->parameters[$name] = $value;
            return $name;
        }
        $count = ++$this->canonicalParameterNamesInUse[$name];
        return $this->addParameter("$name$count", $value);
    }

    public function clearParameters()
    {
        $this->parameters = array();
        return $this;
    }

    public function hasParameters()
    {
        return !empty($this->parameters);
    }

    public function hasParameter($name)
    {
        return isset($this->parameters[$name]);
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getSql()
    {
        return $this->sql;
    }

    public function setSql($sql)
    {
        $this->sql = $sql;
        return $this;
    }

    public function addKeyedField($asKey, $fieldName, $override = false)
    {
        if (false === $override && isset($this->keyFields[$asKey]) && $fieldName !== $this->keyFields[$asKey]) {
            throw new \Exception("Trying to override existing asKey, but not allowed. To enforce this, pass boolean true to third param. Existing $asKey:{$this->keyedFields[$asKey]}, new is $fieldName");
        }
        $this->keyedFields[(string) $asKey] = (string) $fieldName;
        return $this;
    }

    public function setKeyedFields(array $keysToFields)
    {
        foreach ($keysToFields as $asKey => $fieldName) {
            $this->addKeyedField($asKey, $fieldName);
        }
        return $this;
    }

    public function getKeyedFields()
    {
        return $this->keyedFields;
    }

    /**
     * From $this->keyedFields array it builds a string like this:
     * ' field AS key, field2 AS key ' ...
     * If $this->keyedFields is emtpy it returns ' * ': all sql token
     *
     * @param $narrowKeys, set which keys you want to include in select
     * @return string 
     */
    public function getFieldAsKeyString(array $narrowKeys = array())
    {
        $keyedFields = $this->getKeyedFields();

        if (!empty($narrowKeys)) {
            $keyedFields = array_intersect_key($keyedFields, array_flip($narrowKeys));
        }

        if (empty($keyedFields)) {
            return ' * ';
        }
        $fieldsAsKeys = array();
        foreach ($keyedFields as $key => $fieldName) {
            $fieldsAsKeys[] = "$fieldName AS $key";

        }
        return ' ' . implode(",\n ", $fieldsAsKeys) . "\n ";
    }

    public function loadKeyedFields()
    {
        return array();
    }
}
