<?php
namespace Gbili\Miner\Blueprint\Action\GetContents\Contents;

/**
 * 
 * @author gui
 */
interface ContentsFetcherAggregateInterface extends ContentsFetcherInterface 
{
    /**
     * From all registered content fetcherList, try to fetch
     * the content in order of priority
     * if no content fetcher succeeds return false
     * else return the content and update the content fetcher
     * @return mixed:bool|string
     */
    public function fetch(\Gbili\Url\UrlInterface $url);

    /**
     * Returns the fetched content or throws if not
     * fetched
     * @return mixed:boolean|string
     */
    public function getContent();

    /**
     * fetches the content if not already fetched
     * and returns the fetcher that actually
     * fetched it
     */
    public function getUsedFetcher();

    /**
     * If fetcher already in queue, remove it and insert it
     * with the new priority queue
     * Content fetcherList are used to get the content
     * from whatever support is used
     * they need to implement ContentsFetcherInterface
     *
     * @param string the key by which the content fetcher is identified
     */
    public function addFetcher(\Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherInterface $fetcher, $priority=1);

    /**
     * Check whether the content fetcher exist in the aggregate
     * @return boolean
     */
    public function hasFetcher($fetcher);

    /**
     * @return \Zend\Stdlib\PriorityQueue
     */
    public function getFetcherList();

    /**
     * Gets the fetcher of type 
     * @param mixed:string|ContentsFetcherInterface $fetcher the desired fetcher classname or the actual fetcher (would check if it exists)
     * @return mixed:contentfetcher
     */
    public function getFetcher($fetcherClass);
}
