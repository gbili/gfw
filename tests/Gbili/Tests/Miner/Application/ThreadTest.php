<?php
namespace Gbili\Tests\Miner\Application;

class ThreadTest extends \Gbili\Tests\GbiliTestCase
{
    /**
     * Sets up the fixture, for exaple, open a network connection
     * This method is called before a test is executed
     *
     * @return void
     */
    public function setUp()
    {
        $this->rootActionId = 'retrieve website contents';
        $this->rootFirstChildId = 'get website title';
        $this->rootSecondChildId = 'get website links';
        $this->rootSecondChildsFirstChildId = 'get inner pages contents';

        $config = array(
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
                        'data' => '#<title>(?P<title>[^<]+)</title>#ig',
                        'spit_group' => array('title'),
                        'description' => 'get the title',
                    ),
                    $this->rootSecondChildId => array(
                        'parent' => $this->rootActionId,
                        'type' => 'Extract',
                        'match_all' => true,
                        'data' => '#<a.+?href="(?P<link>[^"]+)"#ig',
                        'spit_group' => array('link'),
                    ),
                    $this->rootSecondChildsFirstChildId => array(
                        'type' => 'GetContents',
                        'new_instance_generating_point' => true,
                        'parent' => $this->rootSecondChildId,
                        'parent_input_group' => 'link',
                    ),
                ),
            ),
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
        );
        $serviceManager = new \Zend\ServiceManager\ServiceManager(new \Gbili\Miner\Service\ServiceManagerConfig($config['service_manager']));
        $serviceManager->setService('ApplicationConfig', $config);
        $this->bp = new \Gbili\Miner\Blueprint\ArrayBlueprint($serviceManager);
        $this->bp->init();
    }


    public function testThreadGetsReturnsActionSetOnConstruction()
    {
        $rootAction = $this->bp->getRoot();
        $thread = new \Gbili\Miner\Application\Thread($rootAction);
        $this->assertEquals($rootAction === $thread->getAction(), true);
    }

    public function testPlaceChildIntoFlowPlacesChild()
    {
        $rootAction = $this->bp->getRoot();
        $thread = new \Gbili\Miner\Application\Thread($rootAction);
        $thread->placeChildIntoFlow();
        $this->assertEquals($this->bp->getAction($this->rootFirstChildId) === $thread->getAction(), true);
    }

/*    public function testPlaceChildThenParentThenChildPlacesParentSecondChild()
    {
        $rootAction = $this->bp->getRoot();
        $rootChildrenCollection = clone $rootAction->getChildrenCollection();
        $thread = new \Gbili\Miner\Application\Thread($rootAction);
        $thread->placeChildIntoFlow();
        $thread->placeChildIntoFlow();
        die(var_dump($thread->getAction()->getId()));
        $this->assertEquals($this->bp->getAction($this->rootSecondChildId) === $thread->getAction(), true);
} */
}
