<?php
namespace Gbili\Lang\ISO;

use Gbili\Lang\ISO\Normalizer\Adapter\AbstractAdapter;

/**
 * The normalizer is meant to return a Normalizer adapter
 * 
 * 
 * @author gui
 *
 */
class Normalizer
{	
	/**
	 * 
	 * @var unknown_type
	 */
	const UNKNOWN_LANG = 'Unknown';
	
	/**
	 * the base class name that will be prepended to
	 * default adapter
	 * 
	 * @var unknown_type
	 */
	public static $baseAdapterClassname = 'Normalizer\\Adapter';
	
	/**
	 * This will be used when $adapterClassname
	 * is not specified
	 * 
	 * @var unknown_type
	 */
	public static $defaultAdapterName = 'Db';

	/**
	 * Determines which adapter to return in getInstance()
	 * @var unknown_type
	 */
	public static $adapterName;
	
	/**
	 * Contains the adapter instance
	 * 
	 * @var unknown_type
	 */
	private static $adapterInstance;
	
	/**
	 * 
	 * @return unknown_type
	 */
	private function __construct(){}
	
	/**
	 * This will return the desired adapter instance
	 * specified in self::$adapterClassname
	 * 
	 * @return unknown_type
	 */
	public static function getInstance()
	{
		if (null === self::$adapterInstance) {
			if (null === self::$adapterName) {
				self::$adapterName = self::$defaultAdapterName;
			}
			$className = self::$baseAdapterClassname . '\\' . ucfirst(self::$adapterName);
			self::$adapterInstance = new $className();
			if (!(self::$adapterInstance instanceof AbstractAdapter)) {
				throw new Exception('Error : The adapter must extend Lang_ISO_Normalizer_Adapter_Abstract');
			}
		}
		return self::$adapterInstance;
	}
	
	/**
	 * Force class to create a new instance
	 * when user calls getInstance().
	 * Use this when you update the adapterName
	 * 
	 * @return unknown_type
	 */
	public static function flushInstance()
	{
		self::$adapterInstance = null;
	}
}