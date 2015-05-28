<?php
namespace Gbili\Tests\Miner\Blueprint\Action\GetContents\Contents;

class ContentsFetcherAggregateTest extends \Gbili\Tests\GbiliTestCase
{
    /**
     * Sets up the fixture, for exaple, open a network connection
     * This method is called before a test is executed
     *
     * @return void
     */
    public function setUp()
    {
        $aggregate = new \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherAggregate;
    }

    public function testCanAddFetcher()
    {
        $aggregate = new \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherAggregate;
        $contentsFetcher1 = new MockContentsFetcher1;
        $unsuccessfulFetcher = new MockUnsuccessfulContentsFetcher;
        $aggregate->addFetcher($unsuccessfulFetcher);
        $aggregate->addFetcher($contentsFetcher1);
    }

    public function testAddedFetcherExists()
    {
        $aggregate = new \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherAggregate;
        $contentsFetcher1 = new MockContentsFetcher1;
        $contentsFetcher1Class = get_class($contentsFetcher1);
        $unsuccessfulFetcher = new MockUnsuccessfulContentsFetcher;
        $unsuccessfulFetcherClass = get_class($unsuccessfulFetcher);

        $aggregate->addFetcher($unsuccessfulFetcher);
        $aggregate->addFetcher($contentsFetcher1);
        
        $has = $aggregate->hasFetcher($contentsFetcher1Class);
        $this->assertEquals($has, true);
    }

    public function testReturnsOnlyFetchersResult()
    {
        $aggregate = new \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherAggregate;
        $contentsFetcher1 = new MockContentsFetcher1;
        $aggregate->addFetcher($contentsFetcher1);
        $url = new \Gbili\Url\Url('http://someurl.com/');
        $result = $aggregate->fetch($url);
        $this->assertEquals($result, get_class($contentsFetcher1));
    }

    public function testUnsuccessfulFetcherFetchReturnsFalse()
    {
        $aggregate = new \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherAggregate;
        $unsuccessfulFetcher = new MockUnsuccessfulContentsFetcher;
        $aggregate->addFetcher($unsuccessfulFetcher);
        $url = new \Gbili\Url\Url('http://someurl.com/');
        $result = $aggregate->fetch($url);
        $this->assertEquals($result, false);
    }

    public function testWhenUnsuccessfulFetcherReturnsFalseTheNextSuccessfulFetcherResultIsReturned()
    {
        $aggregate = new \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherAggregate;
        $contentsFetcher1 = new MockContentsFetcher1;
        $unsuccessfulFetcher = new MockUnsuccessfulContentsFetcher;

        $aggregate->addFetcher($unsuccessfulFetcher);
        $aggregate->addFetcher($contentsFetcher1);
        
        $url = new \Gbili\Url\Url('http://someurl.com/');
        $result = $aggregate->fetch($url);
        $this->assertEquals($result, get_class($contentsFetcher1));
    }

    public function testFirstNonFalseResultIsReturned()
    {
        $aggregate = new \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherAggregate;
        $contentsFetcher1 = new MockContentsFetcher1;
        $unsuccessfulFetcher = new MockUnsuccessfulContentsFetcher;

        $aggregate->addFetcher($contentsFetcher1);
        $aggregate->addFetcher($unsuccessfulFetcher);
        
        $url = new \Gbili\Url\Url('http://someurl.com/');
        $result = $aggregate->fetch($url);
        $this->assertEquals($result, get_class($contentsFetcher1));
    }

    public function testHasFetcherDoesNotRemoveTheItem()
    {
        $aggregate = new \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherAggregate;
        $contentsFetcher1 = new MockContentsFetcher1;
        $contentsFetcher1Class = get_class($contentsFetcher1);
        $unsuccessfulFetcher = new MockUnsuccessfulContentsFetcher;
        $unsuccessfulFetcherClass = get_class($unsuccessfulFetcher);

        $aggregate->addFetcher($unsuccessfulFetcher);
        $has = $aggregate->hasFetcher($unsuccessfulFetcherClass);
        $has = $has && $aggregate->hasFetcher($unsuccessfulFetcherClass);
        $this->assertEquals($has, true);
    }

    public function testGetFetcherDoesNotRemoveTheItem()
    {
        $aggregate = new \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherAggregate;
        $contentsFetcher1 = new MockContentsFetcher1;
        $contentsFetcher1Class = get_class($contentsFetcher1);
        $unsuccessfulFetcher = new MockUnsuccessfulContentsFetcher;
        $unsuccessfulFetcherClass = get_class($unsuccessfulFetcher);

        $aggregate->addFetcher($unsuccessfulFetcher);
        $fetcher = $aggregate->getFetcher($unsuccessfulFetcherClass);
        $fetcher = $aggregate->getFetcher($unsuccessfulFetcherClass);
        $this->assertEquals(($fetcher === $unsuccessfulFetcher), true);
    }

    public function testAddFetcherSetsRightPriority()
    {
        $aggregate = new \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherAggregate;
        $contentsFetcher1 = new MockContentsFetcher1;
        $contentsFetcher1Class = get_class($contentsFetcher1);
        $unsuccessfulFetcher = new MockUnsuccessfulContentsFetcher;
        $unsuccessfulFetcherClass = get_class($unsuccessfulFetcher);

        $priority = 6;
        $aggregate->addFetcher($unsuccessfulFetcher, $priority);
        $hasPriority = $aggregate->hasPriority($priority);
        $this->assertEquals($hasPriority, true);
    }

    public function testAddFetcherWithDifferentPriorityRemovesOldAndKeepsNew()
    {
        $aggregate = new \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherAggregate;
        $contentsFetcher1 = new MockContentsFetcher1;
        $unsuccessfulFetcher = new MockUnsuccessfulContentsFetcher;

        $oldPriority = 1;
        $newPriority = 6;
        $aggregate->addFetcher($unsuccessfulFetcher, $oldPriority);
        $aggregate->addFetcher($unsuccessfulFetcher, $newPriority);
        $hasOldPriority = $aggregate->hasPriority($oldPriority);
        $hasNewPriority = $aggregate->hasPriority($newPriority);
        $this->assertEquals($hasOldPriority, false);
        $this->assertEquals($hasNewPriority, true);
    }

    public function testaddedFetchersExecuteInPriorityParam()
    {
        $aggregate = new \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherAggregate;
        $contentsFetcher1 = new MockContentsFetcher1;
        $contentsFetcher2 = new MockContentsFetcher2;
        $contentsFetcher3 = new MockContentsFetcher3;

        $priorityToFetcher = array(1 => $contentsFetcher1, 6 => $contentsFetcher2, 2 => $contentsFetcher3);
        foreach ($priorityToFetcher as $priority => $fetcher) {
            $aggregate->addFetcher($fetcher, $priority);
        }

        //Get the fetcher with the highest priority
        $maxPriority = max(array_keys($priorityToFetcher));
        $fetcherWithMaxPriority = $priorityToFetcher[$maxPriority];

        //Fetch the content and expect the result to be the same as 
        //the one returned by the highest priority fetcher
        $url = new \Gbili\Url\Url('http://someurl.com/');
        $result = $aggregate->fetch($url);

        $this->assertEquals($result, get_class($fetcherWithMaxPriority));

        //Remove the highest priority element
        unset($priorityToFetcher[$maxPriority]);
        $aggregate->removeFetcher($fetcherWithMaxPriority);

        //Get the new highest priority element
        $maxPriority = max(array_keys($priorityToFetcher));
        $fetcherWithMaxPriority = $priorityToFetcher[$maxPriority];

        //Try if it executes the next with the highest priority
        $result = $aggregate->fetch($url);
        $this->assertEquals($result, get_class($fetcherWithMaxPriority));
    }
}
