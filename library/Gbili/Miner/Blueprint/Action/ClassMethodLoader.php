<?php
namespace Gbili\Miner\Blueprint\Action;

use Gbili\Url\Authority\Host;

/**
 * This class is meant to load the Callback or Method class
 * given a path and a path type
 * 
 * @todo use closures
 * @author gui
 *
 */
class ClassMethodLoader
{
	/**
	 * cannot be 0
	 * @var unknown_type
	 */
	const CLASS_TYPE_CALLBACK = 12;
	const CLASS_TYPE_METHOD = 13;
	
	const PATH_TYPE_BASE = 21;
	const PATH_TYPE_DIRECT = 22;
	
	const ERROR_FILE_NOT_FOUND = 34;
	const ERROR_CLASS_NOT_FOUND = 35;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private static $loadedClasses = array();
	
	/**
	 * 
	 * @var unknown_type
	 */
	private static $errors = array();
	
	/**
	 * 
	 * @param unknown_type $path
	 * @param Host $host
	 * @param unknown_type $pathType
	 * @return unknown_type
	 */
	public static function loadCallbackClass($path, Host $host, $pathType = self::PATH_TYPE_BASE)
	{
		return self::loadCMClass($path, $host, $pathType, self::CLASS_TYPE_CALLBACK);
	}
	
	/**
	 * 
	 * @param unknown_type $path
	 * @param Host $host
	 * @param unknown_type $pathType
	 * @return unknown_type
	 */
	public static function loadMethodClass($path, Host $host, $pathType = self::PATH_TYPE_BASE)
	{
		return self::loadCMClass($path, $host, $pathType, self::CLASS_TYPE_METHOD);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public static function getErrors()
	{
		return self::$errors;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public static function methodExists($classNameOrObject, $methodName)
	{
		if (is_object($classNameOrObject)) {
			$classNameOrObject = get_class($classNameOrObject);
		}
		if (!in_array($classNameOrObject, self::$loadedClasses)) {
			throw new ClassMethodLoader\Exception('the class is not loaded yet, load the class and then make sure the method exists');
		}
		return method_exists($classNameOrObject, $methodName);
	}
	
	/**
	 * 
	 * @param unknown_type $path
	 * @param Host $host
	 * @param unknown_type $pathType
	 * @param unknown_type $classType
	 * @return unknown_type
	 */
	public static function loadCMClass($path, Host $host, $pathType = self::PATH_TYPE_DIRECT, $classType = self::CLASS_TYPE_CALLBACK)
	{
		if (!is_string($path)) {
			throw new ClassMethodLoader\Exception('the path must be a string');
		}
		if ($pathType !== self::PATH_TYPE_BASE && $pathType !== self::PATH_TYPE_DIRECT) {
			throw new ClassMethodLoader\Exception('the path type must be : Miner_Persistance_Blueprint_Action_ClassMethodLoader::PATH_TYPE_DIRECT or Miner_Persistance_Blueprint_Action_ClassMethodLoader::PATH_TYPE_BASE');
		}
		if (DIRECTORY_SEPARATOR !== mb_substr($path, -1)) {
			$path .= DIRECTORY_SEPARATOR;
		}
		
		$classTypeName = ($classType === self::CLASS_TYPE_CALLBACK)? 'Callback' : 'Method';
		
		//generate file path
		if ($pathType === self::PATH_TYPE_BASE) {
			$path .= $classTypeName . DIRECTORY_SEPARATOR;
		}
		$sl = str_replace(' ', '', ucwords(str_replace('-', ' ', strtolower($host->getSLDomain()))));
		$tl = ucfirst(strtolower($host->getTLDomain()));
		$fileName = $sl . $tl;
		//(when BASE : path/to/base/Method|Callback/HostCom.php) || (when DIRECT : path/to/direct/HostCom.php)
		$filePath = $path . $fileName . '.php';
		
		//make sure it exists
		if (!file_exists($filePath)) {
			self::$errors[self::ERROR_FILE_NOT_FOUND] = 'The method/callback class file is not accessible or does not exist, given : ' . $filePath;
			return self::ERROR_FILE_NOT_FOUND;
		}
		
		//@todo !important! this is crap, need to see how the callback class names and methods are passed to the blueprint
		//(when BASE : Method|Callback_HostCom) || (when DIRECT : Method|CallabckHostCom)
		$className = $classTypeName . (($pathType === self::PATH_TYPE_BASE)? '_' : '') . $fileName;
		if (!class_exists($className)) {
		    require_once $filePath;
		    if (!class_exists($className)) {
		        self::$errors[self::ERROR_CLASS_NOT_FOUND] = 'The class with name ' .$className . " does not exist in $filePath";
		        return self::ERROR_CLASS_NOT_FOUND;
		    }
		}
		//add the loaded class name to stack
		self::$loadedClasses[] = $className;
		return $className;
	}
}
