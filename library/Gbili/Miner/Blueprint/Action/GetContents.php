<?php
namespace Gbili\Miner\Blueprint\Action;

use Gbili\Miner\Blueprint\Action\GetContents\Callback\Wrapper;
use Gbili\Out\Out;
use Gbili\Url\Url; 
use Gbili\Encoding\Encoding;

/**
 * Get the contents from a url and
 * converts the output to utf8 if needed
 * 
 * @author gui
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
	 * @param Miner\Persistance\Blueprint\Action\GetContents\Callback\Wrapper $cW
	 * @return unknown\type
	 */
	public function setCallbackWrapper(Wrapper $cW)
	{
		$this->callbackWrapper = $cW;
        return $this;
	}

    public function getCallbackWrapper()
    {
        if (!$this->hasCallbackWrapper()) {
            throw new \Exception('has no callback wrapper');
        }
        return $this->callbackWrapper;
    }

    public function hasCallbackWrapper()
    {
        return null !== $this->callbackWrapper;
    }
	
	/**
	 * This type of action never has final results
	 * (non-PHPdoc)
	 * @see Blueprint/Miner\Persistance\Blueprint\Action#hasFinalResults()
	 */
	public function hasFinalResults()
	{
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Blueprint/Miner\Persistance\Blueprint\Action#getResult($groupNumber)
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
     * @todo  the input needs to be a string so: 
     *        1st. check if action has been executed.
     *        2nd A. if it is not the case, dont throw, instead allow event listeners to return a string.
     *        2nd B. else if it returns some imput, pass it to the listeners and return a string
     *
	 * @return string 
	 */
	protected function getInputFromAction()
	{
        $result = $this->getInputAction()->getResult($this->getInputGroup());
        
        //@todo pass the result to listeners and allow them to modify it

        if ($result === null) {
		    throw new Exception("Input action must be of type extract when not root, or Trying to get input from action that has not been executed");
        }

		return $result;
	}
	
	/**
	 * The input can come from three different places
	 * -other action than parent
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
	 * @return unknown\type
	 */
	public function getInput()
	{
		if ($this->canGetInputFromCallback()) {
            return $this->getInputFromCallback();
		}
		return $this->getInputFromAction();
	}
	
    protected function canGetInputFromCallback()
    {
        return $this->hasCallbackWrapper() && $this->getCallbackWrapper()->hasMoreLoops() && null !== $this->lastInput;
    }

	/**
	 * 
	 * @throws Exception
	 */
	protected function getInputFromCallback()
	{
        if (!$this->canGetInputFromCallback()) {
            throw new Exception("loop reached end cannot execute anymore, call clear() || lastInput is null");
        }
        return $this->getCallbackWrapper()->apply($this->lastInput);
	}
	
	/**
	 * 
	 * @return unknown\type
	 */
	protected function innerExecute()
	{
	    if ($this->parentIsExtractButDoesNotHaveTheInputGroupIAmReferringTo()) {
            if (!$this->isOptional()) {
                throw new \Exception('Referring to an input group that does not exist in extract parent resultset');
            }
            return false;
	    }

		$input = $this->getInput();

		$url = new Url($input);
		if (!$url->isValid()) {
			throw new Exception("the url string is not valid given : " . print_r($url));
		}
		
		$result = $this->getContents($url);
		
		if (false === $result) {
			return $this->executionSucceed = false; //throw new Exception('file\get\contents() did not succeed, url : ' . print_r($url->toString(), true));
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
        $c = new GetContents\Contents\Savable();
        $c->setUrl($url);
        $result = $c->getContents();

        //Apply a delay (even if it is after the actual fetching,
        //it will delay the rest of the app, thus next fetch)
        if ($c->isFreshContents()) {
            $this->getBlueprint()->getServiceManager()->get('Delay')->reset()->apply();
        }

        if (false !== $result) {
            $c->save();
        }

	    return $result;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Blueprint/Miner\Persistance\Blueprint\Action#clear()
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
	 * @see Blueprint/Miner\Persistance\Blueprint\Action#spit()
	 */
	public function spit()
	{
		throw new Exception('GetContents actions never have final results, don\'t call spit().');
	}

    public function hydrate(array $info)
    {
        parent::hydrate($info);
        $this->getBlueprint()->initCallbackWrapperForAction($this);
    }
}
