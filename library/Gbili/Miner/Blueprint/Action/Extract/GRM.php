<?php
namespace Gbili\Miner\Blueprint\Action\Extract;

/**
 * GRM : Group Result Mapping
 * 
 * On extract actions, the result groups can be intercepted
 * and spitted as final results.
 * 
 * There are two ways of intercepting the groups witha method : 
 *  1. Together : 
 *  	All concerned groups are passed to the method as an array
 *  	and the method returns one single result that will replace
 *  	the group with the lowest number. All other concerned groups
 *  	will be removed from final result.
 *  	(some groups desapear).
 *  2. OnByOne :
 *  	The concerned groups are passed one by one to the method
 *  	and the result will replace the actual group in final results
 *  	(all groups are kept).
 *  
 *  The OneByOne intercept process takes place before the
 *  Together intercept process, to avoid trying to get
 *  missing groups.
 *  
 *  Once the intercept process has taken place the ones that are
 *  mapped to an entity will be spitted. As the intercept process
 *  removes some groups, this class will make sure that the groups
 *  that get removed are not intended to be spitted.
 * 
 * 
 * @author gui
 *
 */
class GRM
{	
	/**
	 * Contains the group numbers that have been
	 * mapped to some entity
	 * 
	 * @var unknown_type
	 */
	private $groupsMappedToSomeEntity = array();
	
	/**
	 * Contains all groups mapping to their entity
	 * and optional parameter
	 * 
	 * @var unknown_type
	 */
	private $groupToEntityMap = array();
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $groupToInterceptMethodAndTypeMap = array();
	
	/**
	 * Groups already set
	 * 
	 * @var unknown_type
	 */
	private $groupsAlreadyIntercepted = array();
	
	/**
	 * The groups that get intecepted together
	 * will all desapear except the one with
	 * the lowest number.
	 * 
	 * @var unknown_type
	 */
	private $desapearingGroups = array();
	
	/**
	 * Don't make integrity checks twice
	 * if the input hasn't changed
	 * 
	 * @var unknown_type
	 */
	private $integrityChecked = false;
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function __construct()
	{
		$this->groupsAlreadyIntercepted[Method\Wrapper::INTERCEPT_TYPE_ONEBYONE] = array();
		$this->groupsAlreadyIntercepted[Method\Wrapper::INTERCEPT_TYPE_TOGETHER] = array();
	}
	
	/**
	 * 
	 * @param $group
	 * @param $entity
	 * @param $isOptional
	 * @return $this
	 */
	public function spitGroupAsEntity($group, $entity, $isOptional)
	{
		if (!is_int($entity)) {
			throw new GRM\Exception('$entity must be an integer');
		}
		if (!is_bool($isOptional)) {
			throw new GRM\Exception('$isOptional must be a boolean');
		}
		//make sure groups are not repeated
		if (in_array($group, $this->groupsAlreadyIntercepted)) {
			throw new GRM\Exception("The group : '$group' is already set in the basket");
		} else {
			$this->groupsMappedToSomeEntity[] = $group;
		}
		$this->groupToEntityMap[] = array('regexGroup' => $group, 'entity' => $entity, 'isOpt' => $isOptional);
		$this->integrityChecked = false;
		return $this;
	}
	
	/**
	 * 
	 * @param numeric | array $groups
	 * @param unknown_type $methodName
	 * @return unknown_type
	 */
	public function interceptGroupsOneByOne($groups, $methodName)
	{
		if (!is_array($groups)) {
			$groups = array($groups);
		}
		$this->addToInterceptMap($groups, $methodName, Method\Wrapper::INTERCEPT_TYPE_ONEBYONE);
	}
	
	/**
	 * 
	 * @param array $groups
	 * @param unknown_type $methodName
	 * @return unknown_type
	 */
	public function interceptGroupsTogether(array $groups, $methodName)
	{
		$this->addToInterceptMap($groups, $methodName, Method\Wrapper::INTERCEPT_TYPE_TOGETHER);
	}
	
	/**
	 * 
	 * @param array $groups
	 * @param unknown_type $methodName
	 * @param unknown_type $interceptType
	 * @return unknown_type
	 */
	private function addToInterceptMap(array $groups, $methodName, $interceptType)
	{
		if (!is_string($methodName)) {
			throw new GRM\Exception('the methodName must be a string given : ' . print_r($methodName, true));
		}

		$lowestGroup = false;
		foreach ($groups as $group) {
			if (!is_numeric($group)) {
				throw new GRM\Exception('the groups must be passed as a numeric value in an array given : ' . print_r($groups, true));
			}
			$group = (integer) $group;
			//only allow one intercept per group per intercept type
			if (in_array($group, $this->groupsAlreadyIntercepted[$interceptType])) {
				throw new GRM\Exception('you can intercept a group only once per intercept type');
			}
			//add the group to intercepted
			$this->groupsAlreadyIntercepted[$interceptType][] = $group;
			$this->groupToInterceptMethodAndTypeMap[] = array('regexGroup' => (integer) $group, 'methodName' => $methodName, 'interceptType' => $interceptType);
			if (Method\Wrapper::INTERCEPT_TYPE_TOGETHER === $interceptType) {
				if ($lowestGroup === false) {//first time
					$lowestGroup = $group;
				} else if ($lowestGroup > $group) {//set as lowest group
					$this->desapearingGroups[] = $lowestGroup;//add the old lowest group to desapearing
					$lowestGroup = $group;
				} else {//group is higher it will desapear
					$this->desapearingGroups[] = $group;
				}
			}
		}
		$this->integrityChecked = false;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function checkIntegrity()
	{
		//only check if the together intercept type is being used
		if (!empty($this->groupsAlreadyIntercepted[Method\Wrapper::INTERCEPT_TYPE_TOGETHER])) {
			$a = array_intersect($this->groupsMappedToSomeEntity, $this->desapearingGroups);
			if (!empty($a)) {
				throw new GRM\Exception('some groups whose destiny is to desapear are mapped to some entity, groups : ' . print_r($a, true));
			}
		}
		$this->integrityChecked = true;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getGroupToEntityMap()
	{
		if (false === $this->integrityChecked) {
			$this->checkIntegrity();
		}
		return $this->groupToEntityMap;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasGroupToEntityMap()
	{
		return !empty($this->groupToEntityMap);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getGroupToMethodMap()
	{
		if (false === $this->integrityChecked) {
			$this->checkIntegrity();
		}
		return $this->groupToInterceptMethodAndTypeMap;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasGroupToMethodMap()
	{
		return !empty($this->groupToInterceptMethodAndTypeMap);
	}
}