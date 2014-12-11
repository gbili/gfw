<?php
namespace Gbili\Miner\Blueprint\Action\Extract;

/**
 * GroupResultMapping
 * 
 * On Extract actions, the result groups can be intercepted
 * and spitted as final results.
 * 
 * I. Interception
 * ===============
 * There are two ways of intercepting the groups with a method : 
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
 *  One could think that the __OneByOne__ intercept process should take place 
 *  before the __Together__ intercept process, to avoid trying to get
 *  missing groups.
 *  In fact, a priority is set
 *
 *  
 * II. Spitting as final results
 * =============================
 *  Once the intercept process has taken place the ones that are
 *  mapped to an entity will be spitted. 
 *  __Important__: As the intercept process removes some groups, 
 *  this class will make sure that the groups that get removed are 
 *  not intended to be spitted.
 *
 * @todo  The service locator should be passsed to Extract\Method\Wrapper
 * and it should return the instance of the class that contains the method 
 * 
 * 
 * @author gui
 *
 */
class GroupResultMapping
{	
	/**
	 * 
	 * @var unknown_type
	 */
	const NO_INTERCEPT_METHOD = 0;

    /**
     * When defining method interceptions, the user can
     * set this invokable identifier and it will be used
     * by default for all following calls to addToInterceptMap.
     *
     * Contains the identifier in the service manger that
     * can be invoked directly with one param. 
     *
     * @var string
     */
    private $invokableIdentifier = null;

    /**
     * Calls to addToInterceptMap will store the invokableIdentifier
     * as the lastInvokableIdentifier.
     * If the last identifier is different the priority will be
     * increased, otherwise it will be kept the same
     *
     * @var string
     */
    private $lastInvokableIdentifier = null;

    /**
     * Every call to any method (be it type TOGETHER or ONEBYONE
     * has a priority). 
     * The priority is increased if the previous methodName is different
     * that the new one. Or the intercept type is different than
     * the previous one.
     * 
     * @var integer
     */
    private $priority = 0;

    /**
     * Keep a record of the last intercept types
     * this will influence the priority
     *
     * @var integer
     */
    private $lastInterceptType = null;

	/**
	 * Contains the group numbers that have been
	 * mapped to some entity
	 * 
	 * @var array 
	 */
	private $groupsMappedToSomeEntity = array();
	
	/**
	 * Contains all groups mapping to their entity
	 * and optional parameter
	 * 
	 * @var array 
	 */
	private $groupToEntityMap = array();
	
	/**
	 * 
	 * @var array 
	 */
	private $groupToInterceptMethodAndTypeMap = array();
	
	/**
	 * The groups that get intecepted together
	 * will all desapear except the one with
	 * the lowest number.
	 * 
	 * @var array 
	 */
	private $desapearingGroups = array();
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function __construct()
	{
	}

	/**
	 * Proxy + added functionality (allow to set entity and method intercept at same time)
	 * @param mixed $group
	 * @param mixed $entity
	 * @param mixed $param3
	 * @param mixed $param4
	 * @throws Exception
	 * @return \Gbili\Miner\Blueprint\Action\Extract\Savable
	 */
	public function spitGroupAsEntity($group, $entity, $param3 = false, $param4 = self::NO_INTERCEPT_METHOD)
	{
        list($isOptional, $resultInterceptMethod) = $this->handleVariableParamOrder($param3, $param4);

		//set mapping to entity
		if (!is_int($entity)) {
			throw new GroupResultMapping\Exception('$entity must be an integer');
		}
		if (!is_bool($isOptional)) {
			throw new GroupResultMapping\Exception('$isOptional must be a boolean');
		}
	    $this->groupsMappedToSomeEntity[] = $group;
		$this->groupToEntityMap[] = array('regexGroup' => $group, 'entity' => $entity, 'isOpt' => $isOptional);
		
		//allow the result to be intercepted before spitting,
		//this can also be done by calling $this->interceptGroupsOneByOne
		if (self::NO_INTERCEPT_METHOD !== $resultInterceptMethod) {
			if (!is_string($resultInterceptMethod)) {
				throw new Exception('$resultInterceptMethod (4th param) must be a string');
			}
			$this->interceptGroupsOneByOne($group, $resultInterceptMethod);
		}
		return $this;
	}

    /**
     * Figure out which parameter is what
     *
     * @return array
     */
    protected function handleVariableParamOrder($param3, $param4)
    {
		//user wants to use param3 as isOptional and param4 as $resultInterceptMethod
		if (is_bool($param3)) {
			$isOptional = $param3;
			$resultInterceptMethod = (is_string($param4))? $param4 : self::NO_INTERCEPT_METHOD;
		//user used 3 paramter as resultInterceptMethod, and wants the 4th param to be isOptional default value
		} else if (is_string($param3)) {
			$isOptional = (is_bool($param4))? $param4 : false;
			$resultInterceptMethod = $param3;
		} else {
			throw new Exception('3 and 4 Parameter values not supported');
		}
        return array($isOptional, $resultInterceptMethod);
    }

    /**
     * Pass something that can be retrieved from the
     * service manager and that can be invoked directly
     *
     * Every time the identifier is different, save the
     * old identifier as lastInvokableIdentifier
     *
     * @param string $identifier
     * @return \Gbili\Miner\Blueprint\Action\Extract\GroupResultMapping
     */
    public function setInvokableIdentifier($identifier)
    {
        if ($this->invokableIdentifier !== $identifier) {
            $this->lastInvokableIdentifier = $this->invokableIdentifier;
            $this->invokableIdentifier = $identifier;

            $this->increasePriority();
        }
        return $this;
    }

    /**
     * This should be called every time the method name changes
     * or the intercept type changes
     *
     * @return \Gbili\Miner\Blueprint\Action\Extract\GroupResultMapping
     */
    protected function increasePriority()
    {
        $this->priority++;
        return $this;
    }

	
	/**
	 * 
	 * @param numeric | array $groups
	 * @param unknown_type $methodName
	 * @return \Gbili\Miner\Blueprint\Action\Extract\GroupResultMapping
	 */
	public function interceptGroupsOneByOne($groups, $methodName=null)
	{
		if (!is_array($groups)) {
			$groups = array($groups);
		}
		$this->addToInterceptMap($groups, Method\Wrapper::INTERCEPT_TYPE_ONEBYONE, $methodName);
        return $this;
	}
	
	/**
	 * 
	 * @param array $groups
	 * @param string $methodName
	 * @return \Gbili\Miner\Blueprint\Action\Extract\GroupResultMapping
	 */
	public function interceptGroupsTogether(array $groups, $methodName=null)
	{
		$this->addToInterceptMap($groups, Method\Wrapper::INTERCEPT_TYPE_TOGETHER, $methodName);
        return $this;
	}

    /**
     * Passes the method name to update the invokableIdentifier for priority purposes
     * Or takes the invokableIdentifier and sets the methodName without increasing priority
     *
     * If both are null, returns null
     *
     * @return mixed:string|null
     */
    protected function updateInvokableIdentifierOrMethodName($methodName)
    {
        if (null === $methodName) {
            if (null === $this->invokableIdentifier) {
                throw new GroupResultMapping\Exception('You are not passing a invakableIdentifier as param, and the instance one is null too. Either pass the identifier as second param, or call setInvokableIdentifier(identifier)');
            }
            $methodName = $this->invokableIdentifier;
        } else {
            $this->setInvokableIdentifier($methodName);
        }
        return $methodName;
    }

    /**
     * Makes sure method name has something,
     * pdateInvokableIdentifierOrMethodName($mn) can increase $this->priority
     * @return 
     */
    protected function ensureMethodNameIsString($methodName=null)
    {
        $methodName = $this->updateInvokableIdentifierOrMethodName($methodName);

		if (!is_string($methodName)) {
			throw new GroupResultMapping\Exception('the methodName must be a string given : ' . print_r($methodName, true));
		}
        return $methodName;
    }
	
	/**
     * @todo every time that the method name changes, a new call index should be created
     * the call index is equivalent to the priority at which each call has to be made
     * The priority index should be saved alonside the group as "priority"
	 * @param array $groups
	 * @param unknown_type $methodName
	 * @param unknown_type $interceptType
	 * @return unknown_type
	 */
	private function addToInterceptMap(array $groups, $interceptType, $methodName=null)
	{
        $lastPriority = $this->priority;
        $methodName = $this->ensureMethodNameIsString($methodName); //can change priority

        // if the intercept type changes but the priority has not changed, change priority
        // if the intercept type is null, then it means its the first call, so dont increase priority
        if ($this->priority === $lastPriority 
            && $this->lastInterceptType !== $interceptType 
            && $this->lastInterceptType !== null) {
            $this->increasePriority();
        }
        $this->lastInterceptType = $interceptType;

		foreach ($groups as $group) {
			if (!is_numeric($group)) {
				throw new GroupResultMapping\Exception('the groups must be passed as a numeric value in an array given : ' . print_r($groups, true));
			}
			$group = (integer) $group;
			//add the group to intercepted
            $this->groupToInterceptMethodAndTypeMap[] = array(
                'regexGroup' => (integer) $group, 
                'methodName' => $methodName, 
                'interceptType' => $interceptType,
                'priority' => $this->priority,
            );
		}
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getGroupToEntityMap()
	{
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
