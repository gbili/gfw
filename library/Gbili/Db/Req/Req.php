<?php
namespace Gbili\Db\Req;

/**
 * This class allows you to use all the
 * functionallities of AbstractReq
 * and to sort of use a singleton
 * 
 * @author gui
 *
 */
class Req
extends AbstractReq
{
	/**
	 * Allow one instance to be registered at
	 * class level
	 * 
	 * @var unknown_type
	 */
	private static $registeredInstance = null;
	
	/**
	 * 
	 * @param unknown_type $differentPrefixedAdapter
	 * @return unknown_type
	 */
	public function __construct($differentPrefixedAdapter = null)
	{
		parent::__construct($differentPrefixedAdapter);
	}
	
	/**
	 * Register the current instance
	 * in class
	 * It will be returned by getRegisteredInstance()
	 * When using this feature, understand that the 
	 * registered instance can be accessed by anyone
	 * having access to the class, and sometimes this
	 * is ¡not safe!
	 * 
	 * @return unknown_type
	 */
	public function register()
	{
		if (null === self::$registeredInstance) {
			self::$registeredInstance = $this;
		} else {
			if (self::$registeredInstance !== $this) {
				throw new Exception('Your class registry is already filled with another instance, you must call unregister from that instance before being able to register again');
			}
		}
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function unregister()
	{
		//ignore unregister when null
		if (null !== self::$registeredInstance) {
			if (self::$registeredInstance !== $this) {
				throw new Exception('This instance is not allowed to free the class regsitry, you must unregister from the same instance that registered itself');
			}
			unset(self::$registeredInstance);
		}
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function isEmptyRegistry()
	{
		return null === self::$registeredInstance;
	}
	
	/**
	 * Return the registered instance
	 * 
	 * @return unknown_type
	 */
	public static function getRegInstance()
	{
		if (null === self::$registeredInstance) {
			self::$registeredInstance = new self();//will instantiate on falback prefix
		}
		return self::$registeredInstance;
	}
}
