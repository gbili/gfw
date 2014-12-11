<?php
namespace Gbili\Miner\Blueprint\Action\Extract\Method;

use Gbili\Out\Out;

/**
 * This method 
 * 
 * There are two intercept types: TOGETHER or ONEBYONE
 * Actions that use interception, will pass their results
 * to this interception suite 
 *
 * Intercept groups one by one won't modify the results array cardinality
 * Intercept groups together will reduce cardinality to 1
 * 
 * The same method can be used in different priorities
 * The same groups can be intercepted in different method calls (be careful with intercept together
 * That's why the priority is considered as the index.
 *
 * @see \Gbili\Miner\Blueprint\Action\Extract\Method\Wrapper::initializeMethodCallSuite() for more info
 * @author gui
 *
 */
class Wrapper
{
	/**
     * An intercept type is a way to pass the results obtained
     * from an action, to the intercept method
	 * 
	 * @var unknown_type
	 */
	const INTERCEPT_TYPE_TOGETHER = 2;
	const INTERCEPT_TYPE_ONEBYONE = 3;

    /**
     * Intercept types to method name that can
     * handle the intercept type
     *
     * @var array
     */
    protected $interceptTypeToHandleBy = array(
        self::INTERCEPT_TYPE_ONEBYONE => 'interceptGroupsOneByOne',
        self::INTERCEPT_TYPE_TOGETHER => 'interceptGroupsTogether', 
    );

	/**
	 * Contains all invokables 
	 * 
	 * @var \Zend\ServiceManager\ServiceManager
	 */
	private $serviceManager;
	
	/**
	 * Contains each a
	 * 
	 * @var unknown_type
	 */
	private $methodCallSuite = array();
	
	/**
     * Tell the wrapper which groups it should take from the results and pass
     * to each method for a given interceptType
     *
     * There are intercept Types.
     *
	 * @param \Zend\ServiceManager\ServiceManager $serviceManager holding
	 * @param array $mapping
	 * @return void 
	 */
	public function __construct(\Zend\ServiceManager\ServiceManager $serviceManager, array $mapping)
	{
        $this->setServiceManager($serviceManager);

		if (empty($mapping)) {
			throw new Wrapper\Exception('the mapping passed is empty');
		}
        $supportedInterceptTypes = array_keys($this->interceptTypeToHandleBy);
        $this->initializeMethodCallSuite($mapping);
	}

    /**
     * Used for retrieving the invokable methods
     *
     * @return \Gbili\Miner\Action\Extract\Method\Wrapper 
     */
    public function setServiceManager(\Zend\ServiceManager\ServiceManager $sm)
    {
		$this->serviceManager = $sm;
        return $this;
    }

    /**
     * Used for retrieving the invokable methods
     *
	 * @return \Zend\ServiceManager\ServiceManager
     */
    public function getServiceManager()
    {
		$this->serviceManager;
    }

    /**
     * Create the groups and associate them to the method call they will be passed to as args.
     *
     * Method calls are separated by priorities. This means that the same method can be
     * called at diffrent times for the same resultset (although probably but not necesarily
     * on different groups)
     *
     * Every priority can have different methods called in different types: TOGETHER or ONEBYONE
     * Note that the same method can be called in different types: TOGETHER or ONEBYONE in the
     * same priority
     * The return array is:
     * array(
     *     <priority1> => array(
     *         <INTERCEPT_TYPE1> => array(
     *             <call_methodX> => array(group1,group2, group5...),
     *             // different method is called in a same intercept type and 
     *             // same priority (there is no way to know which method is called first 
     *             <call_methodZ> => array(group1,group3, group3...),
     *         ),
     *         <INTERCEPT_TYPE2> => array(
     *             // same method is called in a different intercept type and
     *             // same priority (the priority is determined by the intercept type)
     *             <call_methodX> => array(group1,group2, group5...), 
     *         ),
     *     ),
     *     <priority2> => array(
     *         <INTERCEPT_TYPE2> => array(
     *             // same method is called in different or same intercept type and
     *             // different priority (it will be called once all methods in 
     *             // priority1 have been called)
     *             <call_methodX> => array(group1,group2, group5...),
     *         ),
     *     ),
     *
     * Groups are added to the 
     * @return \Gbili\Miner\Action\Extract\Method\Wrapper 
     */
    public function initializeMethodCallSuite(array $mapping)
    {
        foreach ($mapping as $groupMap) {
            $priority = (integer) $groupMap['priority'];
			$interceptType = (integer) $groupMap['interceptType'];
            $invokableIdentifier = $groupMap['methodName'];

			if (!$this->serviceManager->has($invokableIdentifier)) {
				throw new Wrapper\Exception('the invokable does not exist in serviceManager, invokableIdentifier : ' . $invokableIdentifier);
			}

            if (!is_array($this->methodCallSuite[$priority])) {
                $this->methodCallSuite[$priority] = array();
            }

            if (!isset($this->methodCallSuite[$priority][$interceptType])) {
                $this->methodCallSuite[$priority][$interceptType] = array();
            }

			if (!isset($this->methodCallSuite[$priority][$interceptType][$invokableIdentifier])) {
				$this->methodCallSuite[$priority][$interceptType][$invokableIdentifier] = array();
			}

			$this->methodCallSuite[$priority][$interceptType][$invokableIdentifier][] = $groupMap['regexGroup'];
        }
        ksort($this->methodCallSuite);
        return $this;
    }

	/**
	 * Given some output 
     *
	 * @param array $resultsArray the output of the action
	 * @return array the array with the results intercepted
	 */
	public function intercept(array $resultsArray)
	{
        $priorities = array_keys($this->methodCallSuite);
        foreach ($priorities as $priority) {
            $resultsArray = $this->executeAllMethodsInPriority($priority, $resultsArray);
        }
		return $resultsArray;
	}

    /**
     * All the methods that were registered in this priority are executed
     * in their respective intercept type by passing the results array as arguments.
     * @return array
     */
    protected function executeAllMethodsInPriority($priority, array $resultsArray)
    {
        foreach ($this->interceptTypeToHandleBy as $interceptType => $handleMethod) {
            if (!isset($this->methodCallSuite[$priority][$interceptType])) continue;
            foreach ($this->methodCallSuite[$priority][$interceptType] as $methodName => $groups) {
                $resultsArray = $this->{$handleMethod}($groups, $resultsArray, $methodName);
            }
        }
        return $resultsArray;
    }
	
	/**
     * Get results with same keys as groups
     * pass those results as param to $methodName
     * save the result in a group with the lowest key number
     * Intercept groups one by one won't modify the results array cardinality
     * Intercept groups together will reduce cardinality to 1
     *
	 * 
	 * @param array $groups contains the keys of elements in $resultsArray that should be intercepted
	 * @param array $resultsArray some of the values should be intercepted
	 * @param string $methodName identifier of invokable() in servicemanager to which results should be passed for interception. 
	 * @return array cardinality of results array will be reduced by (count(groups)-1)
	 */
	public function interceptGroupsTogether(array $groups, array $resultsArray, $methodName)
	{
        $lowestGroup = min($groups);
        $groupsAsKeys = array_flip($groups);
        $interceptedResults = array_intersect_key($resultsArray, $groupsAsKeys);
        $notInterceptedResults = array_diff_key($resultsArray, $groupsAsKeys);
        if (count($interceptedResults) !== count($groupsAsKeys)) {
            throw new Wrapper\Exception(
                'Some groups are asked to be intercepted but it is not present in resultsArray anymore (or has never been).'
                . ' Remember that resultsArray looses all concerned groups that are not the lowest in a INTERCEPT_TYPE_TOGETHER call.'
                . ' The groups are: ' . print_r(array_diff_key($groupsAsKeys, $resultsArray), true)
            );
        }
        $returnedResults = $notInterceptedResults;
        $invokable = $this->serviceManager->get($methodName);
		$returnedResults[$lowestGroup] = $invokable($interceptedResults);
		return $returnedResults;
	}
	
	/**
	 * This interception applies the methodName to each result
	 * whose key is in $groups values (one at a time)
	 * and replaces their value with the intercept result
     *
     * The result array will not change cardinality
	 * 
	 * @param array $groups contains the keys of elements in $resultsArray that should be intercepted
	 * @param array $resultsArray some of the values should be intercepted
	 * @param string $methodName identifier of invokable() in servicemanager to which results should be passed for interception. 
	 * @return array same cardinality as resultsArray
	 */
	public function interceptGroupsOneByOne(array $groups, array $resultsArray, $methodName)
	{
		foreach ($groups as $group) {
			if (!isset($resultsArray[$group])) {
				throw new Wrapper\Exception('One group is asked to be intercepted but it is not present in resultsArray anymore (or has never been). Remember that resultsArray looses all concerned groups that are not the lowest in a INTERCEPT_TYPE_TOGETHER call');
			}
			////Out::l2("intercepting result with group : $group, and value : " . print_r($resultsArray[$group], true) . "\n");
            $invokable = $this->serviceManager->get($methodName);
			$resultsArray[$group] = $invokable(array($resultsArray[$group]));//wrap the result in an array
		}
		return $resultsArray;
	}
}
