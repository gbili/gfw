<?php
namespace Gbili\Db;

/**
 * Avoid having lots of Db instances
 * Singletton
 * 
 * 
 * 
 * @author gui
 *
 */
class Registry
{
	/**
	 * If you want to use another
	 * adapter you have to change
	 * this name to what your classes end with
	 * 
	 * Every class will be appended this name
	 * if the second parameter in getInstance is true
	 * 
	 * @var unknown_type
	 */
	private static $classNameEndPart = '\\Db\\Req';
	
	/**
	 * 
	 * @var unknown_type
	 */
	private static $instances;

    protected static $classNameGenerator;
	
	/**
	 * Make it a class static
	 * 
	 * @return unknown_type
	 */
	private function __construct(){}
	
	/**
	 * Get the instance of a given class name
	 * 
	 * @param string | object $className
	 * @return unknown_type
	 */
	public static function getInstance($className)
	{
        $className = self::getReqClassName($className);
		if (!self::hasInstance($className)) {
			self::$instances[$className] = new $className();
		}
		return self::$instances[$className];
	}

    public static function setReqClassNameGenerator($generator)
    {
        if (!is_callable($generator)) {
            throw new Exception('Generator is not callable');
        }
        self::$classNameGenerator = $generator;
    }

    public static function getReqClassNameGenerator()
    {
        if (null === self::$classNameGenerator) {
            self::setReqClassNameGenerator(self::getDefaultReqClassNameGenerator());
        }
        return self::$classNameGenerator;
    }

    public static function getDefaultReqClassNameGenerator()
    {
        return function ($className) {
            //allow the user to pass an instance intead of the class name
            if (is_object($className)) {
                $className = get_class($className);
            }
            //append the end of full class name
            $className = (false === strpos($className, \Gbili\Db\Registry::getClassNameEndPart()))? $className . \Gbili\Db\Registry::getClassNameEndPart() : $className;
            return $className;
        };
    }

    /**
     *
     */
    public static function getReqClassName($className)
    {
        $generator = self::getReqClassNameGenerator();
        return call_user_func($generator, $className);
    }

    /**
     * @return boolean whether there is an instance with that className
     */
    public static function hasInstance($className)
    {
        return isset(self::$instances[self::getReqClassName($className)]);
    }
	
	/**
	 * Add an instance to the registry that needs
	 * parameters to be passed to the constructor
	 * 
	 * @param AbstractReq $instance
	 * @return unknown_type
	 */
	public static function setInstance($instance)
	{
        if (!$instance instanceof \Gbili\Db\Req\AbstractReq) {
            throw new Exception('Instances must subclass \Gbili\Db\Req\AbstractReq');
        }
        $instanceClassName = get_class($instance);
        if ($instanceClassName !== self::getReqClassName($instance)) {
            throw new Exception('Cannot register instances with a differing classNameEndPart');
        }
        if (!self::hasInstance($instance)) {
            self::$instances[self::getReqClassName($instance)] = $instance;
        }
	}
	
	/**
	 * 
	 * @param unknown_type $key
	 * @param unknown_type $requestorInstance
	 * @return unknown_type
	 */
	public static function registerInstance($key, Req\AbstractReq $requestorInstance)
	{
		$className = get_class($requestorInstance);
		//only allow one instance per class
		if (isset(self::$instances[$className])) {
			//drop $requestorInstance from param and get the instance from self::$instances[$className]
			$requestorInstance = self::$instances[$className];
		}
		if (is_object($key)) {
			$key = get_class($key);
		}
		if (!is_string($key)) {
			throw new Exception('You must pass either an object or a string for param $key');
		}
		//now when calling getInstance() it will return $requestorInstance 
		self::$instances[$key . self::$classNameEndPart] = $requestorInstance;
	}
	
	/**
	 * This will get the param $end
	 * and prepend an underscore
	 * 
	 * @param unknown_type $end
	 * @return unknown_type
	 */
	public static function setClassNameEndPart($end)
	{
		if (!is_string($end))  {
			throw new Exception('Error : the setClassNameEndPart() parameter must be a string');
		}
		if ('\\' !== substr($end, 0, 1)) {
		    $end = '\\' . $end;
		}
		self::$classNameEndPart = $end;
	}

    /**
     * @return
     */
    public static function getClassNameEndPart()
    {
        return self::$classNameEndPart;
    }
	
	/**
	 * Get the class names that have currently been registered
	 * 
	 * @return unknown_type
	 */
	public static function getRegisteredClassNames()
	{
		return array_keys(self::$instances);
	}
	
	/**
	 * get an array with className and isntance
	 * 
	 * @return unknown_type
	 */
	public static function getRegisteredInstances()
	{
		return self::$instances;
	}
}
