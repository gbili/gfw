<?php
namespace Gbili\Miner\Blueprint\Action;

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

    public function canGiveInputToAction(AbstractAction $action)
    {
        return $this->isExecuted();
    }

    public function giveInputToAction(AbstractAction $action)
    {
        if (!$this->canGiveInputToAction($action)) {
            throw new \Exception('This action must be executed before being able to give results to anyone');
        }
        return $this->getResult();
    }
	
	/**
	 * 
	 * @return unknown\type
	 */
	protected function innerExecute()
	{
        $this->executionSucceed = false;
        $input = $this->getInputFromAction();

        if ($input) {
            $url = new \Gbili\Url\Url($input);
            if (!$url->isValid()) {
                throw new Exception("the url string is not valid given : " . print_r($url));
            }
            $result = $this->getContents($url);
            
            if (false !== $result) {
                $this->result     = $result;
                $this->lastInput  = $input;
                $this->executionSucceed = true;
            }
        }
		return $this->executionSucceed;
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
	 * @param \Gbili\Url\UrlInterface $url
	 */
	protected function getContents(\Gbili\Url\UrlInterface $url)
	{
        $fetcherAggregate = $this->getFetcherAggregate();
        $fetcherAggregate->fetch($url);

        if (!$result = $fetcherAggregate->getContent()) {
            throw new \Exception('No fetcher was able to fetch contents.');
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
