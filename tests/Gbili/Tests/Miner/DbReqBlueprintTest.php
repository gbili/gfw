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
        $config = array(
            'host'                        => 'shopstarbuzz.com',
            'exect_time_limit'            => 86400,
            'execution_allowed_fails_max_count' => 2,
            'persistance_allowed_fails_max_count' => 1,
            'unpersisted_instances_max_count' => 1,
            'results_per_action_count'    => 5,
            'limited_results_action_id'   => 2, //After 5 pages of category, Switch to next cateogry
            'delay_min'                   => 10,
            'delay_max'                   => 15,
            'service_manager' => array(
                'invokables' => array(
                    'PersistableInstance' => '\StdClass',
                    'Lexer'           => '\Gbili\Tests\Miner\SomeLexer',
                 )
            ),
        );
        $dbReq = new \Gbili\Tests\Miner\Blueprint\Db\Req;
        $serviceManager = new \Zend\ServiceManager\ServiceManager(new \Gbili\Miner\Service\ServiceManagerConfig($config['service_manager']));
        $serviceManager->setService('ApplicationConfig', $config);
        $this->bp = new \Gbili\Miner\Blueprint\DbReqBlueprint($serviceManager);
        $this->bp->setDbReq($dbReq);
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
        $this->assertEquals($this->bp->getRoot() instanceof \Gbili\Miner\Blueprint\Action\RootActionInterface, true);
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
