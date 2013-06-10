<?php
namespace Gbili\Miner\Blueprint\Action;

use Gbili\Miner\Blueprint\Action\GetContents\Callback\Wrapper;
use Gbili\Out\Out;
use Gbili\Url\Url; 
use Gbili\Encoding\Encoding;
use Gbili\Miner\Blueprint\Action\GetContents\Contents\Savable as ContentsSavable;

/**
 * Get the contents from a url and
 * converts the output to utf8 if needed
 * 
 * @author gui
 *
 *
 */
class GetContents
extends AbstractAction
{
	/**
	 * 
	 * @var unknown_type
	 */
	protected $callbackWrapper = null;
	
	/**
	 * 
	 * @var unknown_type
	 */
	protected $result = null;
	
	/**
	 * 
	 * @param Miner_Persistance_Blueprint_Action_GetContents_Callback_Wrapper $cW
	 * @return unknown_type
	 */
	public function setCallbackWrapper(Wrapper $cW)
	{
		$this->callbackWrapper = $cW;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getCallbackWrapper()
	{
		if (!$this->hasCallbackWrapper()) {
			throw new Exception('The callback handler is not set');
		}
		return $this->callbackWrapper;
	}
	
	/**
	 * This type of action never has final results
	 * (non-PHPdoc)
	 * @see Blueprint/Miner_Persistance_Blueprint_Action#hasFinalResults()
	 */
	public function hasFinalResults()
	{
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Blueprint/Miner_Persistance_Blueprint_Action#getResult($groupNumber)
	 */
	public function getResult($groupNumber = null)
	{
		if (!$this->isExecuted()) {
			throw new Exception('You must call execute() before getResult()');
		}
		return $this->result;
	}
	
	/**
	 * 
	 * @param unknown_type $action
	 * @return unknown_type
	 */
	protected function getInputFromAction($action, $groupNumber)
	{
		if (!$action instanceof Extract) {
			throw new Exception("Parent must be of type extract when not root");
		}
		if (!$action->isExecuted()) {
		    throw new Exception('Trying to get input from action that has not been executed');
		}
		if ($this->hasCallbackWrapper()) {
		    return $action->getResults();
		}
		if (null === $groupNumber) {
		    throw new Exception("Action needs a string as input. Array is allowed only when using callback. As it is not the root action you must specify the groupForInputData so it can take a single element from the parent extract element");
		}
		return $action->getResult($groupNumber);
	}
	
	/**
	 * 
	 * @return boolean
	 */
	protected function hasCallbackWrapper()
	{
	    return null !== $this->callbackWrapper;
	}
	
	/**
	 * The input can come from three different places
	 * -other action than parent : $otherInputAction & $otherActionGroupForInputData
	 * -lastInput (in case there is a cw) : $lastInput 
	 * -parent : $inputGroup
	 * 
	 * The normal action flow is that the roots gets input from bostrapInputData
	 * then it executes, and the result is made available for the children
	 * Then the child executes and so on.
	 * However there may be cases, where some action will need to take
	 * input from a child action so it can create more results. That's when
	 * the flow changes for some loops, until the child cannot generate more
	 * results. :/
	 * 
	 * @return unknown_type
	 */
	public function getInput()
	{
		if ($this->hasCallbackWrapper()) {
            return $this->getInputFromCallback();
		}

		if ($this->needsInputFromOtherThanParent()) {
	        return $this->getInputFromAction($this->otherInputAction, $this->otherActionGroupForInputData);
		}
		return $this->getInputFromAction($this->getParent(), $this->groupForInputData);
	}
	
	/**
	 * 
	 * @throws Exception
	 */
	protected function getInputFromCallback()
	{
	    if (!$this->getCallbackWrapper()->hasMoreLoops() || null === $this->lastInput) {
	        throw new Exception("loop reached end cannot execute anymore, call clear() || lastInput is null");
	    }
	    return $this->getCallbackWrapper()->apply($this->lastInput);
	}
	
	/**
	 * If it needs input from other than parent, make
	 * sure the input action has been executed or
	 * return that for the moment it does not need
	 * input from other than parent... maybe later,
	 * when the other action is executed
	 * 
	 * @return boolean
	 */
	protected function needsInputFromOtherThanParent()
	{
	    return null !== $this->otherInputAction && $this->otherInputAction->isExecuted();
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	protected function innerExecute()
	{
	    if ($this->isOptionalAndCannotGetInputFromGroupInExtractParent()) {
	        return false;
	    } 

		$input = $this->getInput();

		$url = new Url($input);
		if (!$url->isValid()) {
			throw new Exception("the url string is not valid given : " . print_r($url));
		}
		
		$result = $this->getContents($url);
		
		if (false === $result) {
			return $this->executionSucceed = false; //throw new Exception('file_get_contents() did not succeed, url : ' . print_r($url->toString(), true));
		}
		
		$this->result     = $result;
		$this->lastInput  = $input;
		
		return $this->executionSucceed = true;
	}
	
	/**
	 * Allow reuse of fetched contents
	 * @param Url $url
	 */
	protected function getContents(Url $url)
	{
        $c = new ContentsSavable();
        $c->setUrl($url);
        $result = $c->getContents();
	    
	    if (!is_string($result)) {
	        $result = $this->getContentsOverWeb($url);
	        if (false !== $result) {
    	        $c->setContents($result);
    	        $c->save();
	        }
	    }
	    return $result;
	}
	
	/**
	 * 
	 * @param Url $url
	 * @return unknown
	 */
	protected function getContentsOverWeb(Url $url)
	{
        $this->getBlueprint()->getServiceManager()->get('Delay')->reset()->apply();
        $result = file_get_contents($url->toString());
        if (false !== $result) {
    	    $result = Encoding::utf8Encode($result);
        }
	    return $result;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Blueprint/Miner_Persistance_Blueprint_Action#clear()
	 */
	protected function innerClear()
	{
		$this->result = null;
		if ($this->hasCallbackWrapper()) {
			$this->getCallbackWrapper()->rewindLoop();
		}
	}
	
	/**
	 * 
	 * @return boolean
	 */
	protected function innerHasMoreResults()
	{
	    return $this->hasCallbackWrapper() && $this->getCallbackWrapper()->hasMoreLoops();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Blueprint/Miner_Persistance_Blueprint_Action#spit()
	 */
	public function spit()
	{
		throw new Exception('GetContents actions never have final results, don\'t call spit().');
	}
}