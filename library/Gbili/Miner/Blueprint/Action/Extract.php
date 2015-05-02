<?php
namespace Gbili\Miner\Blueprint\Action;

use Gbili\Miner\Blueprint\Action\Extract\Method\Wrapper;
use Gbili\Out\Out;

/**
 * This class is meant to extract meaningfull data (data associated to its meaning)
 * from input data fetched from it's parent
 * 
 * @author gui
 *
 */
class Extract extends AbstractAction
{	
	/**
	 * The regex use in the preg_match function
	 * 
	 * @var Miner_Persistance_Regex_String_Abstract
	 */
	private $regexStr;

	/**
	 * Tells whether the array contained in $results
	 * must be used for muyltiple Video Entities
	 * (the results must not be erased) completely from
	 * the function: clear(), instead just the first record
	 * must be shifted.
	 * 
	 * @var boolean
	 */
	private $useMatchAll;

	/**
	 * Contains the regex instance from where all results are fetched
	 * 
	 * @var \Gbili\Regex\Regex
	 */
	private $regex;

	/**
	 * Maps each group of the regex, to an entity
	 * (a final result)
	 * @var unknown_type
	 */
	private $groupToEntityArray;

	/**
	 * Tells if getResult() can be called
	 * 
	 * @var unknown_type
	 */
	private $isResultReady;

	/**
	 * 
	 * @var unknown_type
	 */
	private $hasFinalResults = false;

	/**
	 * 
	 * @var unknown_type
	 */
	private $hasChildAction = null;

	/**
	 * 
	 * @var unknown_type
	 */
	private $currentChildAction = null;

	/**
	 * 
	 * @var unknown_type
	 */
	private $nextStep = null;
	
	/**
	 * 
	 * @param $regex is the regular expression needed to extract the content from the inputData
	 * @param $useMatchAll
	 * @param $groupNumToEntityArray
	 * @param $nextActionInputDataGroupNumber
	 * @return unknown_type
	 */
	public function __construct($useMatchAll = false)
	{
		parent::__construct();
	}

    /**
     * @param $regexStr mixed:string|\Gbili\Regex\String\AbstractString
     * @return self
     */
    public function setRegexStr($regexStr)
    {
		if (is_string($regexStr)) {
			$regexStr = new \Gbili\Regex\String\Generic($regexStr);
		}
		if (!($regexStr instanceof \Gbili\Regex\String\AbstractString)) {
			throw new Extract\Exception('Action extract constructor first parameter must be of type string or instance of Regex_String_Abstract');
		}
		$this->regexStr = $regexStr;
        return $this;
    }

    /**
     * @throws \Exception if not right type or not valid regex
     * @param mixed:\Gbili\Regex\AbstractRegex|null $regex
     */
    public function setRegex($regex)
    {
        if (!$regex instanceof \Gbili\Regex\AbstractRegex) { //bug if use && or || -> call to undefined method "Action\ ()"????
            if (null !== $regex) {
                throw new \Exception('Pass either an instance of AbstractRegex or null');
            }
        }
        $this->regex = $regex;
        return $this;
    }

    /**
     * @param $useMatchAll
     * @return self
     */
    public function setUseMatchAll($useMatchAll = false)
    {
		$this->useMatchAll = (boolean) $useMatchAll;
        return $this;
    }
	
	/**
	 * Maps each group in the regex to an entity in the application
	 * 
	 * if an empty array is passed, it means there will be just one group
	 * and it will be used as input data for the child action
	 * 
	 * @param $groupToEntity
	 * @return void 
	 */
	public function setGroupMapping(array $groupToEntityArray)
	{
		if ($this->hasFinalResults = !empty($groupToEntityArray)) {
			$this->groupToEntityArray = $groupToEntityArray;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Blueprint/Miner_Persistance_Blueprint_Action#hasFinalResults()
	 */
	public function hasFinalResults()
	{
		if (null === $this->hasFinalResults) {
			throw new Extract\Exception('You must call setGroupMapping() before hasFinalResults()');
		}
		return $this->hasFinalResults;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Blueprint/Miner_Persistance_Blueprint_Action#spit()
	 */
	public function spit()
	{
		if (!$this->hasFinalResults()) {
			throw new Extract\Exception('This action has no final results, therefor it cannot spit, call hasFinalResults() before calling spit() to avoid exception.');
		}
	    $this->isResultReadyOrThrow();
		return $this->getEntityToResultMap();
	}
	
	/**
	 * 
	 * @throws Extract\Exception
	 */
	protected function getEntityToResultMap()
	{
	    $entityToResult = array();
	    $results        = $this->getResults();
	    foreach ($this->groupToEntityArray as $k => $array) {
	        if (isset($results[$array['regexGroup']]) && !empty($results[$array['regexGroup']])) {
	            $entityToResult[$array['entity']] = $results[$array['regexGroup']];
	        } else if (0 === $array['isOpt']) {
	            throw new Extract\Exception('There is a result that is required and it is missing : ' . print_r($results, true) . ' mapping : ' . print_r($this->groupToEntityMapping));
	        }
	    }
	    return $entityToResult;
	}

	/**
	 * Make regex object contain some results
	 * 1. Has been executed
	 * 		a. is not MatchAll -> throw up (only one execution per result)
	 * 		b. is MatchAll	   -> advance the pointer to the next result so getResult() can return it
	 * 2. Has not been executed
	 * 		a. is MatchAll
	 * 		b. is not MatchAll
	 * @todo having a property interceptedResults, would avoid reinterception on every call to getResult, if that property existed, it should be set to null on every exec 
	 * @note isResultReady replaces isExecuted
	 * @return boolean
	 */
	protected function innerExecute()
	{
	    if ($this->isExecuted()) {
	        return $this->manageMatchAllEarlierResults();
	    }

	    if ($this->parentIsExtractButDoesNotHaveTheInputGroupIAmReferringTo()) {
            if (!$this->isOptional()) {
                throw new \Exception(
                    'Action ' . get_class($this) . ' : ' . $this->getTitle() . ' InputGroup: ' . print_r($this->getInputGroup(), true) . "\n"
                    . 'Parent' . get_class($this->getParent()) . ': ' . $this->getParent()->getTitle() . "\n"
                    . 'Referring to an input group: ' . var_export($this->getInputGroup()) .  ' that does not exist in extract parent resultset');
            }
            return false;
	    }
    	
		$parentInput = $this->getInput();

        $this->getEventManager()->trigger(
            'executeInput',
            $this,
            [
                'input' => $parentInput,
            ]
        );

		$this->setRegex(new \Gbili\Regex\Regex($parentInput, $this->regexStr));
		$this->lastInput = $parentInput;
		
        $this->regex->execute($this->useMatchAll);
		return $this->isResultReady = $this->regex->isValid();
		//@todo after the regex is applied, a callback should be allowed to attempt to refactor the output
	}

	/**
	 * 
	 * @throws Exception
	 */
	public function getInput()
	{
		if (!$this->knowsWhereToGetInputFrom()) {
			throw new Exception('call setGroupForInputData($group), when the parentAction is an instance of Extract');
    	}
	    return $this->getParent()->getResult($this->getInputGroup());
	}

	/**
	 * 
	 * @return boolean
	 */
	protected function knowsWhereToGetInputFrom()
	{
	    return !($this->getParent() instanceof Extract && !$this->hasInputGroup());
	}

	/**
	 * When when the extract instance has already been
	 * executed, it _should be_ because it is using matchAll.
	 * So when calling getResults, this instance will
	 * automatically return the next results of the matchAll
	 * result set (that is, only clear() has been called)
	 * 
	 * @throws Extract\Exception
	 */
	protected function manageMatchAllEarlierResults()
	{
	    if (!$this->useMatchAll) {
	        throw new Extract\Exception('You are trying to execute the same action twice whereas it is not useMatchAll call clear()');
	    }
	    return $this->isResultReady = $this->regex->goToNextMatch();
	}

	/**	
	 * 
	 */
	public function getResult($groupIdentifier = null)
	{
		if (null === $groupIdentifier) {
			throw new Extract\Exception('The group number cannot be null (getResult() first param)');
		}

		$results = $this->getResults();
		
        $responses = $this->getEventManager()->trigger(
            'interceptResult', //event identifier
            $this, //targed
            [ // params
                'results' => $results, 
                'groupIdentifier' => $groupIdentifier,
            ]
        );

        $interceptedResults = $responses->last();
        if (null !== $interceptedResults) {
            $results = $interceptedResults;
        }

		if (!isset($results[$groupIdentifier])) {
            throw new Extract\Exception(
                'The group: ' . $groupIdentifier. ', does not exist in results: ' 
                . print_r($results, true)
                . $this->toString()
            );
		}

		return $results[$groupIdentifier];	
	}

	/**
	 * 
	 * @param mixed:integer|string $groupIdentifier
	 * @return boolean
	 */
	public function hasGroup($groupIdentifier)
	{
	    $this->isResultReadyOrThrow();
		return $this->regex->hasGroup($groupIdentifier);
	}

    protected function isResultReadyOrThrow()
    {
		if (false === $this->isResultReady) {
            if (null !== $this->regex && !$this->regex->isValid()) {
                throw new Extract\Exception('Regex did not match a crap!');
            }
			throw new Extract\Exception('You must call execute() before getResult()');
		}
    }
      

	/**
	 * Return all the results
	 * 
	 * @return unknown_type
	 */
	public function getResults()
	{	
        $this->isResultReadyOrThrow();
		//return all groups of current match, if match all only get current match
	    return $this->regex->getCurrentMatch();
	}
	
	/**
	 * 
	 */
	protected function innerClear()
	{
		$this->isResultReady = false;
		$this->setRegex(null);
	}
	
	/**
	 * 
	 * @return boolean
	 */
	protected function innerHasMoreResults()
	{
	    return true === $this->useMatchAll && true === $this->regex->hasMoreMatches();
	}

    /**
     * Every time an action has a parent of type extract,
     * set the input group of that child action.
     *
     * @param $action AbstractAction
     */
    public function addChild(AbstractAction $action)
    {
        parent::addChild($action);
        $info = $action->getHydrationInfo();
        $action->setInputActionInfo($this, $info['inputGroup']);
    }

    /**
     * @see parent
     */
    public function hydrate(array $info)
    {
        parent::hydrate($info);
        $this->setRegexStr($info['data']);
        $this->setUseMatchAll(1 === (integer) $info['useMatchAll']);
    }
}
