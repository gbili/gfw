<?php
namespace Gbili\Miner\Lexer;

/**
 * Allow easy retrieval of lexer instances.
 * 
 * There must only be one lexer per entity class 
 * meaning that if you are dumping videos, you 
 * will have created a video class, and there 
 * will be only one lexer instance for all the videos 
 * instances. If you are dumping appartements, 
 * hotel rooms, flights etc. there will be one 
 * lexer class for each of these and one lexer instance
 * per lexer class.
 * 
 * @see Miner_Persistance_Lexer_Abstract
 * 
 * 
 * 
 * Singletton
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
	 * 
	 * @var unknown_type
	 */
	private static $classNameEndPart = '\\Lexer';
	
	/**
	 * 
	 * @var unknown_type
	 */
	private static $instances;
	
	/**
	 * Make it a class static
	 * 
	 * @return unknown_type
	 */
	private function __construct(){}
	
	/**
	 * Get the instance of a given class name
	 * 
	 * @param unknown_type $className
	 * @return unknown_type
	 */
	public static function getInstance($className)
	{
		if (is_object($className)) {
			$className = get_class($className);
		}
		//append the end of full class name
		$className .= self::$classNameEndPart;
		if (!isset(self::$instances[$className])) {
			$instance = new $className();
			if (!($instance instanceof AbstractLexer)) {
				throw new Exception($className . ' must be an instance of Miner_Persistance_Lexer_Abstract');
			}
			self::$instances[$className] = $instance;
		}
		return self::$instances[$className];
	}
	
	/**
	 * Add an instance to the registry that needs
	 * parameters to be passed to the constructor
	 * 
	 * @param unknown_type $instance
	 * @return unknown_type
	 */
	public static function setInstance($instance)
	{
		self::$instances[get_class($instance)] = $instance;
	}
	
	/**
	 * This will get the param $end
	 * and prepend an underscore
	 * 
	 * @param unknown_type $end
	 * @return unknown_type
	 */
	public static function setClassnameEndPart($end)
	{
		if (!is_string($end))  {
			throw new Exception('Error : the setClassnameEndPart() parameter must be a string');
		}
		self::$classNameEndPart = $end;
	}
	
	/**
	 * Get the class names that have currently been registered
	 * 
	 * @return unknown_type
	 */
	public static function getRegisteredClassnames()
	{
		return array_keys(self::$instances);
	}
	
	/**
	 * get an array with classname and isntance
	 * 
	 * @return unknown_type
	 */
	public static function getRegisteredInstances()
	{
		return self::$instances;
	}
}