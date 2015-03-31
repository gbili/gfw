<?php
namespace Gbili\Tests\Miner\Blueprint\Action;

class ActionTest extends \Gbili\Tests\GbiliTestCase
{
    /**
     * Sets up the fixture, for exaple, open a network connection
     * This method is called before a test is executed
     *
     * @return void
     */
    public function setUp()
    {
        $host = new \Gbili\Url\Authority\Host('shopstarbuzz.com');
        $dbReq = new \Gbili\Tests\Miner\Blueprint\Db\Req;
        $this->bp = new \Gbili\Miner\Blueprint($host, new \Zend\ServiceManager\ServiceManager, $dbReq);
        $this->bp->init();
    }


    /**
     * @exceptionExpected
     */
    public function testActionMustHaveInputBeforeCallToExecute()
    {
        $action = new \Gbili\Miner\Blueprint\Action\GetContents();
        $action->setId(1);
        $action->execute();
    }

    /**
     * @exceptionExpected
     */
    public function testActionMustExecuteBeforeCallToGetResult()
    {
        $action = new \Gbili\Miner\Blueprint\Action\GetContents();
        $action->setId(1);
        $action->getResult();
    }

    public function testRootActionTakesInputAndReturnsIt()
    {
        $action = new \Gbili\Miner\Blueprint\Action\GetContents\RootGetContents();
        $urlString = 'http://somedomain.com';
        $action->setBootstrapData($urlString);
        $this->assertEquals($urlString, $action->getInput());
    }

    /**
     * @expectedException
     */
    public function testRootActionNeedsInput()
    {
        $action = new \Gbili\Miner\Blueprint\Action\GetContents\RootGetContents();
        $urlString = 'http://somedomain.com';
        $action->setBootstrapData($urlString);
        $this->assertEquals($urlString, $action->getInput());
    }

    /**
     * Tears down the fixture, for example, close a network connection
     * This method is called after a test is executed
     *
     * @return void
     */
    public function tearDown()
    {

    }

    public function testBlueprintReturnsRootAction()
    {
        $root = $this->bp->getRoot();
    }
}
