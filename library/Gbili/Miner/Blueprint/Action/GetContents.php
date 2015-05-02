<?php
namespace Gbili\Miner\Blueprint\Action;

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
implements \Gbili\Miner\ContentsFetcherAggregateAwareInterface
{

    /**
     * @var \Gbili\Miner\Blueprint\Action\GetContents\ContentsInterface the class responsible for getting the contents
     */
    protected $fetcherAggregate;
	
	/**
	 * 
	 * @var unknown_type
	 */
	protected $result = null;
	
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
     * @todo the input needs to be a string so: 
     *       1st. check if action has been executed.
     *       2nd A. if it is not the case, dont throw, instead allow event listeners to return a string.
     *       2nd B. else if it returns some imput, pass it to the listeners and return a string
     *
	 * @return string 
	 */
	protected function getInputFromAction()
	{
        $result = $this->getInputAction()->getResult($this->getInputGroup());
        if ($result === null) {
		    throw new Exception("Input action must be of type extract when not root, or Trying to get input from action that has not been executed");
        }

        //Allow other input action refactoring
        $responses = $this->getEventManager()->trigger(
            'inputFromAction', //event identifier
            $this, //targed
            ['input' => $result], // params
            function ($listenerReturn) {return is_string($listenerReturn);} //Meets our expected result, will setStopped
        );
        if ($responses->stopped()) {
            $result = $responses->last();
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
        $responses = $this->getEventManager()->trigger(
            'input', //event identifier
            $this,
            ['lastInput' => $this->lastInput],
            function ($listenerReturn) {return is_string($listenerReturn);} //Meets our expected result, will setStopped
        );
        if ($responses->stopped()) {
            return $responses->last();
        }
		return $this->getInputFromAction();
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

        $this->getEventManager()->trigger(
            'executeInput',
            $this,
            [
                'input' => $input,
            ]
        );

		$url = new Url($input);
		if (!$url->isValid()) {
			throw new Exception("the url string is not valid given : " . print_r($url));
		}
		
		$result = $this->getContents($url);
		
		if (false === $result) {
            return $this->executionSucceed = false;
        }
		
		$this->result     = $result;
		$this->lastInput  = $input;
		
		return $this->executionSucceed = true;
	}

    /**
     * The object used to get the contents from whatever support
     *
     * @return \Gbili\Miner\Blueprint\Action\GetContents\ContentsInterface
     */
    public function getFetcherAggregate()
    {
        if (null === $this->fetcherAggregate) {
            throw new \Exception('No fetcher aggregate was set');
        }
        return $this->fetcherAggregate;
    }

    /**
     * The object used to get the contents from whatever support
     *
     * @param \Gbili\Miner\Blueprint\Action\GetContents\ContentsInterface
     * @return self
     */
    public function setFetcherAggregate(\Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherAggregateInterface $fetcherAggregate)
    {
        $this->fetcherAggregate = $fetcherAggregate;
        return $this;
    }
	
	/**
	 * Allow reuse of fetched contents
	 * @param Url $url
	 */
	protected function getContents(Url $url)
	{
        $fetcherAggregate = $this->getFetcherAggregate();
        $fetcherAggregate->fetch($url);

        if (!$result = $fetcherAggregate->getContent()) {
            throw new \Exception('No fetcher was able to fetch contents.');
        }

        //@TODO trigger an event in the fetcher aggregate when contents are found that fetchers can listen to, and treat accordingly
        if (get_class($fetcherAggregate->getUsedFetcher()) === '\Gbili\Miner\Blueprint\Action\GetContents\Contents\FileGetContents') {
            //Save the contents to db for next time
            $savable = $fetcherAggregate->getFetcher('\Gbili\Miner\Blueprint\Action\GetContents\Contents\Savable');
            $savable->setUrl($url);
            $savable->setContents($result);
            $savable->save();
            //Apply a delay (even if it is after the actual fetching,
            //it will delay the rest of the app, thus next fetch)
            $this->getBlueprint()->getServiceManager()->get('Delay')->reset()->apply();
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
	}
	
	/**
	 * 
	 * @return boolean
	 */
	protected function innerHasMoreResults()
	{
        //Allow callbacks to tell whether they can provide more urls or not 
        $responses = $this->getEventManager()->trigger(
            'generateMoreResults',
            $this,
            [],
            function ($listenerReturn) {return $listenerReturn;} //Meets our expected result, will setStopped
        );
        $moreResults = false;
        if ($responses->stopped()) {
            $moreResults = $responses->last();
        }
        return $moreResults;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Blueprint/Miner\Persistance\Blueprint\Action#spit()
	 */
	public function spit()
	{
		throw new Exception('GetContents actions never have final results, don\'t call spit().');
	}
}
