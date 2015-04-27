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

    public function testAddFetcherUpdatesPriority()
    {
        $aggregate = new \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherAggregate;
        $contentsFetcher1 = new MockContentsFetcher1;
        $contentsFetcher1Class = get_class($contentsFetcher1);
        $unsuccessfulFetcher = new MockUnsuccessfulContentsFetcher;
        $unsuccessfulFetcherClass = get_class($unsuccessfulFetcher);

        $aggregate->addFetcher($unsuccessfulFetcher, 1);
        $aggregate->addFetcher($unsuccessfulFetcher, 6);
        $hasPriority = $aggregate->getFetcherList()->hasPriority(6);
        $this->assertEquals($hasPriority, true);
    }
}
