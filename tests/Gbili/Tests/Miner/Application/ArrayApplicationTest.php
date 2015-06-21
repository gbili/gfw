<?php
namespace Gbili\Tests\Miner\Application;

class ArrayApplicationTest extends \Gbili\Tests\GbiliTestCase
{
    static public $counter;

    protected function initApp()
    {
        $this->rootActionId = 'retrieve website contents';
        $this->rootFirstChildId = 'get website title';
        $this->rootSecondChildId = 'get website links';
        $this->rootSecondChildsFirstChildId = 'get inner pages contents';

        $config = array(
            'blueprint'                   => array(
                'type' => 'array',
            ),
            'host'                        => 'shopstarbuzz.com',
            'exect_time_limit'            => 86400,
            'execution_allowed_fails_max_count' => 2,
            'persistance_allowed_fails_max_count' => 1,
            'unpersisted_instances_max_count' => 1,
            'results_per_action_count'    => 5,
            'limited_results_action_id'   => $this->rootSecondChildId, //After 5 pages of category, Switch to next cateogry
            'delay_min'                   => 10,
            'delay_max'                   => 15,
            'service_manager' => array(
                'invokables' => array(
                    'PersistableInstance' => '\StdClass',
                    'Lexer'           => '\Gbili\Tests\Miner\SomeLexer',
                 ),
                 'factories' => array(
                    'LoggerVarDump' => function ($sm) { 
                        return new \Zend\Stdlib\CallbackHandler(function($e) { 
                            echo '-----' . \Gbili\Tests\Miner\Application\ArrayApplicationTest::$counter++ . '---' . PHP_EOL;
                            echo 'Name: ' . PHP_EOL;
                            var_dump($e->getName());
                            echo 'Id: ' . PHP_EOL;
                            var_dump($e->getTarget()->getId());
                            echo 'Params: ' . PHP_EOL;
                            var_dump($e->getParams());
                            echo 'Target: ' . PHP_EOL;
                            var_dump(get_class($e->getTarget()));
                            echo '- - - - - -' . PHP_EOL;
                        });
                    },
                ),
            ),
            'contents_fetcher_aggregate' => array(
                'queue' => array(
                    10 => array(
                        new \Gbili\Tests\Miner\Blueprint\Action\GetContents\Contents\MockContentsFetcherWebPage,
                        new \Gbili\Tests\Miner\Blueprint\Action\GetContents\Contents\MockContentsFetcherLink,
                    ),
                ),
            ),
            'action' => array(
                'rules' => array(
                    $this->rootActionId => array(
                        'type' => 'GetContents',
                        'data' => 'http://somedomain.com',
                        'description' => 'retrieve the website contents from the web',
                    ),
                    $this->rootFirstChildId => array(
                        'parent' => $this->rootActionId,
                        'type' => 'Extract',
                        'data' => '<title>(?P<title>[^<]+)</title>',
                        'spit_group' => array('title'),
                        'description' => 'get the title',
                    ),
                    $this->rootSecondChildId => array(
                        'parent' => $this->rootActionId,
                        'type' => 'Extract',
                        'match_all' => true,
                        'data' => '<a.+?href="(?P<link>[^"]+)"',
                        'spit_group' => array('link'),
                    ),
                    $this->rootSecondChildsFirstChildId => array(
                        'type' => 'GetContents',
                        'new_instance_generating_point' => true,
                        'parent' => $this->rootSecondChildId,
                        'input_group' => 'link',
                    ),
                ),
                'listeners' => array(
                    /*'application' => array(
                        array('manageFail.normalAction', 'DumpActionId', 100),
                        array('executeAction.success', 'ActionSuccess', 100),
                    ),*/
                    //@TODO make sure these listeners get hooked to the right actions
                    //either through shared event manager or with some id checking etc.
                    //Way 1 to add listeners
                    $this->rootActionId => array(
                        array('execute.post', 'LoggerVarDump', 2),
                        array('hasMoreResults', 'LoggerVarDump', 2),
                        array('executeInput', 'LoggerVarDump', 2),
                    ),
                    //Way 2
                    array($this->rootFirstChildId, 'execute.post', 'LoggerVarDump', 2),
                    array($this->rootFirstChildId, 'hasMoreResults', 'LoggerVarDump', 2),
                    array($this->rootFirstChildId, 'executeInput', 'LoggerVarDump', 2),

                    array($this->rootSecondChildId, 'execute.post', 'LoggerVarDump', 3),
                    array($this->rootSecondChildId, 'hasMoreResults', 'LoggerVarDump', 3),
                    array($this->rootSecondChildId, 'executeInput', 'LoggerVarDump', 3),
                    array($this->rootSecondChildsFirstChildId, 'execute.post', 'LoggerVarDump', 2),
                    array($this->rootSecondChildsFirstChildId, 'hasMoreResults', 'LoggerVarDump', 2),
                    array($this->rootSecondChildsFirstChildId, 'executeInput', 'LoggerVarDump', 2),
                ),
            ),
        );
        $this->app = \Gbili\Miner\Application\Application::init($config);
    }

    /**
     * Sets up the fixture, for exaple, open a network connection
     * This method is called before a test is executed
     *
     * @return void
     */
    public function setUp()
    {
        $this->initApp();
    }

    public function testAppInitReturnsAppInstance()
    {
        $this->assertEquals(true, $this->app instanceof \Gbili\Miner\Application\Application);
    }

    public function testAppHasFlowEvaluator()
    {
        $this->assertEquals(true, $this->app->getFlowEvaluator() instanceof \Gbili\Miner\Application\FlowEvaluator);
    }

    public function testAppCanBeRun()
    {
        $this->app->run();
    }
}
