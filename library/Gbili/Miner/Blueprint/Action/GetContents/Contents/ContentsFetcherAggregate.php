<?php
namespace Gbili\Miner\Blueprint\Action\GetContents\Contents;

/**
 * 
 * @author gui
 *
 */
class ContentsFetcherAggregate
implements ContentsFetcherAggregateInterface
{
    /**
     * @var string the actual content
     */
    protected $content;

    /**
     * @var string the content fetcher that successfully fetched the content
     */
    protected $usedFetcher;

    /**
     *
     * @var \Zend\Stdlib\SplPriorityQueue
     */
    protected $fetcherList;

    public function __construct()
    {
        $this->fetcherList = new \Zend\Stdlib\PriorityQueue();
    }

    /**
     * From all registered content fetcherList, try to fetch
     * the content in order of priority
     * if no content fetcher succeeds return false
     * else return the content and update the content fetcher
     * @return mixed:bool|string
     */
    public function fetch(\Gbili\Url\UrlInterface $url)
    {
        $content = false;
        foreach ($this->fetcherList->getIterator() as $fetcher) {
            if ($content = $fetcher->fetch($url)) {
                $this->content = $content;
                $this->usedFetcher = $fetcher;
                break;
            }
        }
        return $content;
    }

    /**
     * Returns the fetched content or throws if not
     * fetched
     * @return mixed:boolean|string
     */
    public function getContent()
    {
        if (null === $this->content) {
            throw new \Exception('No content was fetched, call fetch()');
        }
        return $this->content;
    }

    /**
     * fetches the content if not already fetched
     * and returns the fetcher that actually
     * fetched it
     */
    public function getUsedFetcher()
    {
        if (null === $this->content) {
            throw new \Exception('No content was fetched, call fetch');
        }
        return $this->usedFetcher;
    }

    /**
     * If fetcher already in queue, remove it and insert it
     * with the new priority queue
     * Content fetcherList are used to get the content
     * from whatever support is used
     * they need to implement ContentsFetcherInterface
     *
     * @param string the key by which the content fetcher is identified
     */
    public function addFetcher(\Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherInterface $fetcher, $priority=1)
    {
        if ($this->hasFetcher($fetcher)) {
            $this->fetcherList->remove($fetcher);
        }
        $this->fetcherList->insert($fetcher, $priority);
        return $this;
    }

    /**
     * Check whether the content fetcher exist in the aggregate
     * @return boolean
     */
    public function hasFetcher($fetcher)
    {
        if ($fetcher instanceof \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherInterface) {
            return $this->fetcherList->contains($fetcher);
        }
        if (!is_string($fetcher)) {
            throw new \Exception('$fetcher needs to be instance of ContentsFetcherInterface or a classname');
        }
        $fetcherClass = $fetcher;
        foreach ($this->fetcherList->getIterator() as $item) {
            if (get_class($item) === $fetcherClass) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return \Zend\Stdlib\PriorityQueue
     */
    public function getFetcherList()
    {
        return $this->fetcherList;
    }

    /**
     * Gets the fetcher of type 
     * @param mixed:string|ContentsFetcherInterface $fetcher the desired fetcher classname or the actual fetcher (would check if it exists)
     * @return mixed:contentfetcher
     */
    public function getFetcher($fetcherClass)
    {
        if (!is_string($fetcherClass)) {
            throw new \Exception('$fetcher needs to be a classname string');
        }
        foreach ($this->fetcherList->getIterator() as $item) {
            if (get_class($item) === $fetcherClass) {
                return $item;
            }
        }
        throw new \Exception('No fetcher with this class was added: ' . $fetcherClass);
    }
}
