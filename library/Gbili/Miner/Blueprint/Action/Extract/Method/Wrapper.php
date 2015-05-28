<?php
namespace Gbili\Miner\Blueprint\Action\Extract\Method;

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

    protected $interceptTypes = array(
        self::INTERCEPT_TYPE_TOGETHER => 'interceptGroupsTogether', 
        self::INTERCEPT_TYPE_ONEBYONE => 'interceptGroupsOneByOne',
    );

	/**
	 * The instance that contains all methods
	 * 
	 * @var \Zend\ServiceManager\ServiceManager 
	 */
	private $serviceManager;
	
	/**
	 * 
	 * @var array 
	 */
	private $interceptTypeToMethodToGroupMap = array();
	
	/**
	 * 
	 * @param stdClass $methodClassInstance
	 * @param array $mapping
	 * @return void
	 */
	public function __construct(\Zend\ServiceManager\ServiceManager $sm, array $mapping)
	{
        $this->serviceManager = $sm;
		if (empty($mapping)) {
			throw new Wrapper\Exception('the mapping passed is empty');
		}
		//reformat mapping, note that is being passed ordered by intercept type ASC, methodName ASC, regexGroup ASC
		foreach ($mapping as $groupMap) {
            $this->addToInterceptTypeToMethodGroupMap($groupMap['interceptType'], $groupMap['serviceIdentifier'], $groupMap['methodName'], $groupMap['regexGroup']);
		}
	}

    protected function addToInterceptTypeToMethodGroupMap($interceptType, $serviceIdentifier, $methodName, $regexGroup)
    {
        $interceptType = $this->getInterceptTypeOrThrow($interceptType);

        $this->existstsServiceWithMethodOrThrow($serviceIdentifier, $methodName);
        
        $this->initInterceptTypeToMethodToGroupMapArray($interceptType, $serviceIdentifier, $methodName);
        $this->interceptTypeToMethodToGroupMap[$interceptType][$serviceIdentifier][$methodName][] = $regexGroup;
    }

    protected function initInterceptTypeToMethodToGroupMapArray($interceptType, $serviceIdentifier, $methodName)
    {
        if (!isset($this->interceptTypeToMethodToGroupMap[$interceptType])) {
            $this->interceptTypeToMethodToGroupMap[$interceptType] = array();
        }
        if (!isset($this->interceptTypeToMethodToGroupMap[$interceptType][$serviceIdentifier])) {
            $this->interceptTypeToMethodToGroupMap[$interceptType][$serviceIdentifier] = array();
        }
        if (!isset($this->interceptTypeToMethodToGroupMap[$interceptType][$serviceIdentifier][$methodName])) {
            $this->interceptTypeToMethodToGroupMap[$interceptType][$serviceIdentifier][$methodName] = array();
        }
    }

    protected function existstsServiceWithMethodOrThrow($serviceIdentifier, $methodName)
    {
        if (!$this->serviceManager->has($serviceIdentifier)) {
            throw new Wrapper\Exception('Service does not exist. Identifier: '. $serviceIdentifier);
        }
        if (!method_exists($this->serviceManager->get($serviceIdentifier), $methodName)) {
            throw new Wrapper\Exception('the method does not exist in service: '.$serviceIdentifier.', methodName : ' . $methodName);
        }
    }

    protected function getInterceptTypeOrThrow($interceptType)
    {
        $interceptType = (integer) $interceptType;
        if (!in_array($interceptType, array_keys($this->interceptTypes))) {
            throw new Wrapper\Exception('intercept type not supported');
        }
        return $interceptType;
    }
	
	/**
	 * 
	 * @param array $result
	 * @return unknown_type
	 */
	public function intercept(array $resultsArray)
	{
        foreach ($this->interceptTypes as $interceptType => $interceptMethod) {
            if (!isset($this->interceptTypeToMethodToGroupMap[$interceptType])) {
                continue;
            }
            foreach ($this->interceptTypeToMethodToGroupMap[$interceptType] as $serviceIdentifier => $methodToGroupsOrGroup) {
                foreach ($methodToGroupsOrGroup as $methodName => $groupsOrGroup) {
                    $resultsArray = $this->{$interceptMethod}($groupsOrGroup, $resultsArray, $serviceIdentifier, $methodName);
                }
            }
        }
		return $resultsArray;
	}
	
	/**
     * intercept the groups together and change the resultsArray to a single element array
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
	public function interceptGroupsTogether(array $groups, array $resultsArray, $serviceIdentifier, $methodName)
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
		$resultsArray[$lowestGroup] = $this->serviceManager->get($serviceIdentifier)->{$methodName}($concernedResults);
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
	public function interceptGroupsOneByOne(array $groups, array $resultsArray, $serviceIdentifier, $methodName)
	{
        $service = $this->serviceManager->get($serviceIdentifier);
		foreach ($groups as $group) {
			if (!isset($resultsArray[$group])) {
				throw new Wrapper\Exception('One group is asked to be intercepted but it is not present in resultsArray anymore (or has never been). Remember that resultsArray looses all concerned groups that are not the lowest in a INTERCEPT_TYPE_TOGETHER call');
			}
			$resultsArray[$group] = $service->{$methodName}(array($resultsArray[$group]));//wrap the result in an array
		}
		return $resultsArray;
	}
}
