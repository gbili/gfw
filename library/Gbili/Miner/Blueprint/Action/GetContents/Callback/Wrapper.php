<?php
namespace Gbili\Miner\Blueprint\Action\GetContents\Callback;

/**
 * This class serves as a wrapper for the Miner_Persistance_Blueprint_Action_GetContents_Callback_Abstract
 * subclasses.
 * 
 * This instantiates the subclass, sets the input mapping
 * and calls the callback() method and passes it the input
 * 
 * @author gui
 *
 */
class Wrapper
{
	const TYPE_LOOP = 34;
	const TYPE_REFACTORINPUT = 35;

	/**
	 * Mapps the input groups to a parameter
	 * number in the callback method
	 * 
	 * @var unknown_type
	 */
	private $paramToGroupArray = null;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $callbackInstance;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $methodName = null;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $hasMoreLoops = null;
	
	/**
	 * Instantiates the right class and creates the params
	 * array, that will be passed to the subclass applyCallback() method
	 * 
	 * @param unknown_type $callbackClassname
	 * @param array $paramToGroupArray
	 * @return unknown_type
	 */
	public function __construct(AbstractCallback $callbackInstance, $methodName)
	{
		$this->callbackInstance = $callbackInstance;
		$this->callbackInstance->throwIfMethodNotExists($methodName);
		$this->methodName = $methodName;
		//initialize loop state
		$this->callbackInstance->setMethodLoopReachedEndState($methodName, false);
		$this->callbackInstance->setMethodLoopIsFirstTime($methodName, true);
	}
	
	/**
	 * 
	 * @param array $paramToGroupArray
	 * @return unknown_type
	 */
	public function setParamToGroupMapping(array $paramToGroupArray)
	{
		if (null !== $this->paramToGroupArray) {
			throw new Wrapper\Exception("the param to group map array is already set");
		}
		//ensure array is numerically indexed and in order
		ksort($paramToGroupArray);
		if (array_keys($paramToGroupArray) !== range(0, count($paramToGroupArray) - 1)) {
			throw new Wrapper\Exception('You must pass a numerically indexed array from 0 to n, given : ' . print_r($paramToGroupArray));
		}
		$this->paramToGroupArray = $paramToGroupArray;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function apply($input)
	{
		//the callback was already applied and there are no more loops
		if (!$this->hasMoreLoops()) {
			throw new Wrapper\Exception("The loop in method : $thid->methodName reached end so cannot call apply anymore"); 
		}
		//create an array of numerically ordered params with the input
		if (is_array($input)) {
			if (null === $this->paramToGroupArray) {
				throw new Wrapper\Exception('You must call setParamToGroupMapping($array) before calling apply, when the input is an array');
			}
			$orderedParams = self::puzzleValuesWithKeys($this->paramToGroupArray, $input);
		} else if (is_string($input)) {
			$orderedParams = array($input); //wrap the input to make it look as the first an only argument for callback
		} else {
			throw new Wrapper\Exception('You must either provide an array or a string to apply($input)');
		}
		//pas the ordered params array and call the callback
		$mName = $this->methodName;
		$callbackResult = $this->callbackInstance->$mName($orderedParams);
		if (!is_string($callbackResult)) {
			throw new Wrapper\Exception("The method $this->methodName() for class : " . print_r(get_class($this->callbackInstance), true) . ' must return a string. Current return value : ' . print_r($callbackResult, true));
		}
		//the method should be setting its loop state throws otherwise
		$this->hasMoreLoops = $this->hasMoreLoops();
		return $callbackResult;
	}
	
	/**
	 * Returns the keys of param1 and the values of array2
	 * by coupleing the values of param1 array, to the keys of param2
	 * that are the same 
	 * Overrides the $arrayGroups keys with numerically indexed ones
	 * from 0 to count($arrayGroups) - 1
	 * 
	 * @param unknown_type $arrayGroups
	 * @param unknown_type $arrayValues
	 * @return unknown_type
	 */
	public static function puzzleValuesWithKeys($arrayGroups, $arrayValues)
	{
		//this only works because arrayGroups have been numerically ordered from 0 to count -1
		$orderedParams = array();
		foreach ($arrayGroups as $group) {
			if (!isset($arrayValues[$group])) {
				throw new Wrapper\Exception('The groups in callbackMapper array dont match the groups in parent results array');
			}
			$orderedParams[] = $arrayValues[$group];
		}
		return $orderedParams;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function rewindLoop()
	{
		$this->callbackInstance->setMethodLoopReachedEndState($this->methodName, false);
		$this->__callbackInstance->setMethodLoopIsFirstTime($this->methodName, true);
	}
	
	/**
	 * this function will allways return true
	 * if you don't set a value to $this->hasMoreLoops
	 * now it is set when you call apply()
	 * 
	 * @return unknown_type
	 */
	public function hasMoreLoops()
	{
		if (null === $this->hasMoreLoops) {
			return true;
		}
		return !$this->callbackInstance->getMethodLoopReachedEndState($this->methodName);
	}
}
