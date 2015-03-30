<?php
namespace Gbili\Tests\Miner;

class BlueprintTest extends \Gbili\Tests\GbiliTestCase
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
        $dbReq = new Blueprint\Db\Req;
        $this->bp = new \Gbili\Miner\Blueprint($host, new \Zend\ServiceManager\ServiceManager, $dbReq);
        $this->bp->init();
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
        $isRootAction = $this->bp->getRoot() instanceof \Gbili\Miner\Blueprint\Action\RootAction;
        $this->assertEquals($isRootAction, true);
    }

    /**
     * @expectedException \Gbili\Miner\Exception
     */
    public function testThrowsWhenGetActionWithBadIdId()
    {
        $missingActionId = 12341234;
        $this->bp->getAction($missingActionId);
    }
}
