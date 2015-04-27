<?php
namespace Gbili\Tests\Miner\Blueprint\Action\GetContents\Contents;

class SavableTest extends \Gbili\Tests\GbiliTestCase
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
        $contentsFetcher1 = new MockContentsFetcher1;
        $unsuccessfulFetcher = new MockUnsuccessfulContentsFetcher;
        $aggregate->addFetcher($unsuccessfulFetcher);
        $aggregate->addFetcher($contentsFetcher1);
    }

    public function testNothing()
    {
        $this->assertEquals('', '');
    }

    /**
     * Tears down the fixture, for example, close a network connection
     * This method is called after a test is executed
     *
     * @return void
     */
    public function tearDown()
    {
        \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable::clearOrder();
    }
}
