<?php
namespace Gbili\Miner\Blueprint\Action\GetContents\Callback;

/**
 * This class serves as a wrapper for the Miner_Persistance_Blueprint_Action_GetContents_Callback_Abstract
 * subclasses.
 * 
 * This instantiates the subclass, sets the input mapping
 * and calls the callback() and passes it the input
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
	 * number in the callback
	 * 
	 * @var unknown_type
	 */
	private $paramToGroupArray = null;
	
	/**
	 * 
	 * @var string 
	 */
	private $invokableIdentifier = null;
	
	/**
	 * 
	 * @var mixed: null|boolean 
	 */
	private $hasMoreLoops = null;

	/**
	 * Contains all invokables 
	 * 
	 * @var \Zend\ServiceManager\ServiceManager
	 */
	private $serviceManager;

	/**
	 * Instantiates the right class and creates the params
	 * array, that will be passed to the subclass applyCallback() 
     *
	 * @param \Zend\ServiceManager\ServiceManager $serviceManager holding
	 * @param array $paramToGroupArray
	 * @return void 
	 */
    public function __construct(\Zend\ServiceManager\ServiceManager $serviceManager, $invokableIdentifier)
	{
        $this->setServiceManager($serviceManager);

		$this->invokableIdentifier = $invokableIdentifier;
		//initialize loop state
        $this->invokable = $this->getServiceManager()->get($invokableIdentifier);
		$this->invokable->setLoopReachedEndState(false);
		$this->invokable->setLoopIsFirstTime(true);
	}

    /**
     * Used for retrieving the invokable 
     *
     * @return \Gbili\Miner\Action\Extract\Method\Wrapper 
     */
    public function setServiceManager(\Zend\ServiceManager\ServiceManager $sm)
    {
		$this->serviceManager = $sm;
        return $this;
    }

    /**
     * Used for retrieving the invokable 
     *
	 * @return \Zend\ServiceManager\ServiceManager
     */
    public function getServiceManager()
    {
		$this->serviceManager;
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
			throw new Wrapper\Exception("The loop in method : $thid->invokableIdentifier reached end so cannot call apply anymore"); 
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
		//pass the ordered params array and call the callback
		$callbackResult = $this->invokable($orderedParams);
		if (!is_string($callbackResult)) {
			throw new Wrapper\Exception("The invokable $this->invokableIdentifier must return a string. Current return value : " . print_r($callbackResult, true));
		}
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
        $this->invokable
            ->setLoopReachedEndState(false)
            ->setLoopIsFirstTime(true);
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
		$this->hasMoreLoops = ((null === $this->hasMoreLoops) || !$this->invokable->getLoopReachedEndState());
	}
}
