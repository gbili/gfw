<?php
namespace Gbili\Tests\Miner\Application;

class ApplicationTest extends \Gbili\Tests\GbiliTestCase
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
            'limited_results_action_id'   => 6, //After 5 pages of category, Switch to next cateogry
            'delay_min'                   => 10,
            'delay_max'                   => 15,
            'service_manager' => array(
                'invokables' => array(
                    'PersistableInstance' => '\StdClass',
                    'Lexer' => '\Gbili\Tests\Miner\SomeLexer',
                 )
            ),
        );
        $this->app = \Gbili\Miner\Application\Application::init($config);
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

    public function testAppInitReturnsAppInstance()
    {
        $this->assertEquals($this->app instanceof \Gbili\Miner\Application\Application, true);
    }

    public function testInitAppCanBeRun()
    {
        $this->app->run();
    }
}
