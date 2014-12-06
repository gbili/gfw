<?php
namespace Gbili\Miner\Blueprint\Action;

use Gbili\Miner\Blueprint\Action\Extract\Method\Wrapper;
use Gbili\Regex\String\AbstractString;
use Gbili\Regex\String\Generic;
use Gbili\Out\Out;
use Gbili\Regex\Regex;


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
	 * @var Regex
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
	 * Contains the method wrapper that will
	 * intercept the results
	 * 
	 * @var unknown_type
	 */
	private $methodWrapper = null;
	
	/**
	 * 
	 * @param $regex is the regular expression needed to extract the content from the inputData
	 * @param $useMatchAll
	 * @param $groupNumToEntityArray
	 * @param $nextActionInputDataGroupNumber
	 * @return unknown_type
	 */
	public function __construct($regexStr, $useMatchAll = false)
	{
		parent::__construct();
		if (is_string($regexStr)) {
			$regexStr = new Generic($regexStr);
		}
		if (!($regexStr instanceof AbstractString)) {
			throw new Extract\Exception('Action extract constructor first parameter must be of type string or instance of Regex_String_Abstract');
		}
		$this->regexStr    = $regexStr;
		$this->useMatchAll = (boolean) $useMatchAll;
		$this->initInterceptHook();
	}
	
	/**
	 * 
	 */
	protected function initInterceptHook()
	{
	    $this->methodInterceptGroupMapping[Wrapper::INTERCEPT_TYPE_TOGETHER] = array();
	    $this->methodInterceptGroupMapping[Wrapper::INTERCEPT_TYPE_ONEBYONE] = array();
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function setMethodWrapper(Wrapper $mW)
	{
		$this->methodWrapper = $mW;
	}
	
	/**
	 * Maps each group in the regex to an entity in the application
	 * 
	 * if an empty array is passed, it means there will be just one group
	 * and it will be used as input data for the child action
	 * 
	 * @param $groupToEntity
	 * @return unknown_type
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
	 * @todo having a property interceptedResults, would avoid reinterceptation on every call to getResult, if that property existed, it should be set to null on every exec 
	 * @note isResultReady replaces isExecuted
	 * @return boolean
	 */
	protected function innerExecute()
	{
	    if ($this->isExecuted()) {
	        return $this->manageMatchAllEarlierResults();
	    }
	    
	    if ($this->isOptionalAndCannotGetInputFromGroupInExtractParent()) {
	        return false;
	    } 
    	
		$parentInput     = $this->getInput();
		$this->regex     = new Regex($parentInput, $this->regexStr);
		$this->lastInput = $this->regex->getInputString();
		
		$method = ($this->useMatchAll)? 'matchAll' : 'match';
		return $this->isResultReady = (boolean) $this->regex->{$method}();
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
	    return $this->getParent()->getResult($this->groupForInputData);
	}
	
	/**
	 * 
	 * @return boolean
	 */
	protected function knowsWhereToGetInputFrom()
	{
	    return !($this->getParent() instanceof Extract && null === $this->groupForInputData);
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
		$res = $this->getResults();
		
		if (!isset($res[$groupIdentifier])) {
            throw new Extract\Exception(
                'The group: ' . $groupIdentifier. ', does not exist in results: ' 
                . print_r($res, true)
                . $this->toString()
            );
		}

		return $res[$groupIdentifier];	
	}
	
	/**
	 * 
	 * @param unknown_type $groupIdentifier
	 * @return unknown_type
	 */
	public function hasGroup($groupIdentifier)
	{
	    $this->isResultReadyOrThrow();
		return $this->regex->hasGroup($groupIdentifier);
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
		return $this->getResultsAllowInterception();
	}
	
	/**
	 * Allow the user to intercept the results and modify them with some method
	 * @return \Gbili\Miner\Blueprint\Action\
	 */
	protected function getResultsAllowInterception()
	{
        $results = $this->regex->getCurrentMatch();
	    return (null === $this->methodWrapper)? $results : $this->methodWrapper->intercept($results);
	}
	
	/**
	 * 
	 * @throws Extract\Exception
	 */
	protected function isResultReadyOrThrow()
	{
		if (false === $this->isResultReady) {
		    if (null !== $this->regex && !$this->regex->isValid()) {
		        throw new Extract\Exception('regex is not valid.');
		    }
			throw new Extract\Exception('You must call execute() before getResult()');
		}
	}
	
	/**
	 * 
	 */
	protected function innerClear()
	{
		$this->isResultReady = false;
		$this->regex = null;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	protected function isResultReadyForChild()
	{
	    return true === $this->useMatchAll && true === $this->isResultReady;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	protected function innerHasMoreResults()
	{
	    return true === $this->useMatchAll && true === $this->regex->hasMoreMatches();
	}
}
