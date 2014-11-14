<?php
namespace Gbili;

/**
 * The normalizer is meant to return a Normalizer adapter
 * 
 * 
 * @author gui
 *
 */
class Country_Normalizer
{	
	/**
	 * 
	 * @var unknown_type
	 */
	const UNKNOWN_COUNTRY = 'Unknown';

	/**
	 * Contains the lang with which the movie
	 * that has not successfully been normalized
	 * will be instanciated
	 * 
	 * @var unknown_type
	 */
	const DEFAULT_LANG = 'en';
	
	/**
	 * the base class name that will be prepended to
	 * default adapter
	 * 
	 * @var unknown_type
	 */
	public static $baseAdapterClassname = 'Country_Normalizer_Adapter';
	
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
			$className = self::$baseAdapterClassname . '_' . ucfirst(self::$adapterName);
			self::$adapterInstance = new $className();
			if (!(self::$adapterInstance instanceof Country_Normalizer_Adapter_Abstract)) {
				throw new Country_Normalizer_Adapter_Exception('Error : The adapter must extend Country_Normalizer_Adapter_Abstract');
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