<?php
namespace Gbili\Tests\Miner\Blueprint\Action;

class GetContentsTest extends \Gbili\Tests\GbiliTestCase
{
    /**
     * Sets up the fixture, for exaple, open a network connection
     * This method is called before a test is executed
     *
     * @return void
     */
    public function setUp()
    {
    }

    public function testRootActionTakesInputAndReturnsIt()
    {
        $action = new \Gbili\Miner\Blueprint\Action\GetContents;
        $action->hydrate([
            'actionId' => 'some action id',
            'title' => 'some action title',
            'isOpt' => false,
        ]);
    }

    public function testRootActionCanExecuteIfHasBootstrapData()
    {
        $action = new \Gbili\Miner\Blueprint\Action\GetContents\RootGetContents();
        $aggr = new \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherAggregate();
        $aggr->addFetcher(new \Gbili\Tests\Miner\Blueprint\Action\GetContents\Contents\MockContentsFetcher1());
        $action->setFetcherAggregate($aggr);
        $urlString = 'http://somedomain.com';
        $action->setBootstrapData($urlString);
        $action->execute();
    }

    public function testCanGetResultIfExecuted()
    {
        $action = new \Gbili\Miner\Blueprint\Action\GetContents\RootGetContents();
        $aggr = new \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherAggregate();
        $aggr->addFetcher(new \Gbili\Tests\Miner\Blueprint\Action\GetContents\Contents\MockContentsFetcher1());
        $action->setFetcherAggregate($aggr);
        $urlString = 'http://somedomain.com';
        $action->setBootstrapData($urlString);
        $action->execute();
        $action->getResult();
    }

    /**
     * @expectedException \Gbili\Miner\Blueprint\Action\GetContents\Exception
     */
    public function testRootActionNeedsInputBeforeCallToExecute()
    {
        $action = new \Gbili\Miner\Blueprint\Action\GetContents\RootGetContents();
        $action->execute();
    }
}
