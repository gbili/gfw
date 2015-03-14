<?php
namespace Gbili\Miner\Blueprint\Action\Extract\Method;

use Gbili\Miner\Blueprint\Action\ClassMethodLoader;
use Gbili\Out\Out;

/**
 * 
 * @author gui
 *
 */
class Wrapper
{
	/**
	 * 
	 * @var unknown_type
	 */
	const INTERCEPT_TYPE_TOGETHER = 2;
	const INTERCEPT_TYPE_ONEBYONE = 3;

	/**
	 * The instance that contains all methods
	 * 
	 * @var unknown_type
	 */
	private $methodClassInstance;
	
	/**
	 * 
	 * 
	 * @var unknown_type
	 */
	private $interceptTypeToMethodToGroupMap = array();
	
	/**
	 * 
	 * @param stdClass $methodClassInstance
	 * @param array $mapping
	 * @return unknown_type
	 */
	public function __construct($methodClassInstance, array $mapping)
	{
		////Out::l2('created a method wrapper' . "\n");
		if (empty($mapping)) {
			throw new Wrapper\Exception('the mapping passed is empty');
		}
		//reformat mapping, note that is being passed ordered by intercept type ASC, methodName ASC, regexGroup ASC
		foreach ($mapping as $groupMap) {
			$interceptType = (integer) $groupMap['interceptType'];
			if ($interceptType !== self::INTERCEPT_TYPE_TOGETHER && $interceptType !== self::INTERCEPT_TYPE_ONEBYONE) {
				throw new Wrapper\Exception('intercept type not supported');
			}
			if (!ClassMethodLoader::methodExists(get_class($methodClassInstance), $groupMap['methodName'])) {
				throw new Wrapper\Exception('the method does not exist in method class, methodName : ' . $groupMap['methodName'] . ', methodClassInstance : ' . print_r($methodClassInstance, true));
			}
			if (!isset($this->interceptTypeToMethodToGroupMap[$interceptType])) {
				$this->interceptTypeToMethodToGroupMap[$interceptType] = array();
			}
			if (!isset($this->interceptTypeToMethodToGroupMap[$interceptType][$groupMap['methodName']])) {
				$this->interceptTypeToMethodToGroupMap[$interceptType][$groupMap['methodName']] = array();
			}
			$this->interceptTypeToMethodToGroupMap[$interceptType][$groupMap['methodName']][] = $groupMap['regexGroup'];
		}
		$this->methodClassInstance = $methodClassInstance;
	}
	
	/**
	 * 
	 * @param array $result
	 * @return unknown_type
	 */
	public function intercept(array $resultsArray)
	{
		////Out::l2("MethodMethod is intercepting results : \n" . print_r($resultsArray, true));
		//intercept the groups together and change the resultsArray to a single element array
		if (isset($this->interceptTypeToMethodToGroupMap[self::INTERCEPT_TYPE_TOGETHER])) {
			////Out::l2("intercepting groups together : \n" . print_r($this->interceptTypeToMethodToGroupMap[self::INTERCEPT_TYPE_TOGETHER], true));
			foreach ($this->interceptTypeToMethodToGroupMap[self::INTERCEPT_TYPE_TOGETHER] as $methodName => $groups) {
				$resultsArray = $this->interceptGroupsTogether($groups, $resultsArray, $methodName);
			}
		}
		//results array has been modified by interceptGroupsTogether, so it may have less results
		if (isset($this->interceptTypeToMethodToGroupMap[self::INTERCEPT_TYPE_ONEBYONE])) {
			////Out::l2("intercepting groups one by one : \n" . print_r($this->interceptTypeToMethodToGroupMap[self::INTERCEPT_TYPE_ONEBYONE], true));
			foreach ($this->interceptTypeToMethodToGroupMap[self::INTERCEPT_TYPE_ONEBYONE] as $methodName => $group) {
				$resultsArray = $this->interceptGroupsOneByOne($group, $resultsArray, $methodName);
			}
		}
		return $resultsArray;
	}
	
	/**
	 * This function returns an array that combines
	 * the elements of resultsArray that have the same
	 * keys than the values in groups. It will unset
	 * all groups in $resultsArray that are not the
	 * lowest group. and replace the lowest group
	 * with the method call result.
	 * interceptConcernedGroupsAndReplaceSecondParamLowestGroupWithResult
	 * 
	 * @param unknown_type $groups
	 * @param unknown_type $resultsArray
	 * @return unknown_type
	 */
	public function interceptGroupsTogether(array $groups, array $resultsArray, $methodName)
	{
		$concernedResults = array();
		$lowestGroup = null;
		foreach ($groups as $group) {
			if (!isset($resultsArray[$group])) {
				throw new Wrapper\Exception('One group is asked to be intercepted but it is not present in resultsArray anymore (or has never been). Remember that resultsArray looses all concerned groups that are not the lowest in a INTERCEPT_TYPE_TOGETHER call');
			}
			$concernedResults[] = $resultsArray[$group];
			if ($lowestGroup === null) {
				$lowestGroup = $group;
			} else if ($group < $lowestGroup) {
				unset($resultsArray[$lowestGroup]);
				$lowestGroup = $group;
			} else {
				unset($resultsArray[$group]);
			}
		}
		$resultsArray[$lowestGroup] = $this->methodClassInstance->$methodName($concernedResults);
		return $resultsArray;
	}
	
	/**
	 * This method applies the method to all results
	 * whose key is in $groups values (one at a time)
	 * and replaces their value with the intercept result
	 * 
	 * @param array $groups
	 * @param array $resultsArray
	 * @param unknown_type $methodName
	 * @return array
	 */
	public function interceptGroupsOneByOne(array $groups, array $resultsArray, $methodName)
	{
		foreach ($groups as $group) {
			if (!isset($resultsArray[$group])) {
				throw new Wrapper\Exception('One group is asked to be intercepted but it is not present in resultsArray anymore (or has never been). Remember that resultsArray looses all concerned groups that are not the lowest in a INTERCEPT_TYPE_TOGETHER call');
			}
			////Out::l2("intercepting result with group : $group, and value : " . print_r($resultsArray[$group], true) . "\n");
			$resultsArray[$group] = $this->methodClassInstance->$methodName(array($resultsArray[$group]));//wrap the result in an array
		}
		return $resultsArray;
	}
}
