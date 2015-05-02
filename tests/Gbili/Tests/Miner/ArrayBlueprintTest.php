<?php
namespace Gbili\Tests\Miner;

class ArrayBlueprintTest extends \Gbili\Tests\GbiliTestCase
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
            'blueprint_type'              => 'array',
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
                 ),
            ),
            'action_set' => array(
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
            'listeners' => array(
                array($this->rootFirstChildId, 'execute.post', 'strtoupper', 1),
                array($this->rootFirstChildId, 'execute.post', 'strtolower', 2),
            ),
        );
        $serviceManager = new \Zend\ServiceManager\ServiceManager(new \Gbili\Miner\Service\ServiceManagerConfig($config['service_manager']));
        $serviceManager->setService('ApplicationConfig', $config);
        $this->bp = new \Gbili\Miner\Blueprint\ArrayBlueprint($serviceManager);
        $this->bp->init();
    }

    public function testServiceManagerIsSetOnConstruction()
    {
        $this->assertEquals($this->bp->getServiceManager() instanceof \Zend\ServiceManager\ServiceManager, true);
    }

    public function testKnowsWhichActionInArrayConfigIsRoot()
    {
        $this->assertEquals($this->bp->getRoot()->getId(), $this->rootActionId);
    }

    public function testRootIsOfTypeRootActionInterface()
    {
        $this->assertEquals($this->bp->getRoot() instanceof \Gbili\Miner\Blueprint\Action\RootActionInterface, true);
    }

    public function testRootShouldHaveTwoChildren()
    {
        $collection = $this->bp->getRoot()->getChildrenCollection();
        $this->assertEquals(count($collection), 2);
    }

    public function testRootTwoChildrenHaveTheRightIds()
    {
        $collection = $this->bp->getRoot()->getChildrenCollection();
        $collection->rewind();
        $rootFirstChild = $collection->getNext();//first child
        $this->assertEquals($rootFirstChild->getId(), $this->rootFirstChildId);
        $rootSecondChild = $collection->getNext();//second child
        $this->assertEquals($rootSecondChild->getId(), $this->rootSecondChildId);
    }

    public function testSecondChildShouldHaveOneChild()
    {
        $collection = $this->bp->getRoot()->getChildrenCollection();
        $collection->rewind();
        $collection->getNext(); //first child
        $rootSecondChild = $collection->getNext();//second child
        $secondChildChildrenCollection = $rootSecondChild->getChildrenCollection();
        $this->assertEquals(count($secondChildChildrenCollection), 1);
    }

    public function testSecondChildsFirstChildHasTheRightId()
    {
        $collection = $this->bp->getRoot()->getChildrenCollection();
        $collection->rewind();
        $collection->getNext(); //first child
        $rootSecondChild = $collection->getNext();//second child
        $secondChildChildrenCollection = $rootSecondChild->getChildrenCollection();
        $secondChildChildrenCollection->rewind();
        $rootSecondChildsFirstChild = $secondChildChildrenCollection->getNext();
        $this->assertEquals($rootSecondChildsFirstChild->getId(), $this->rootSecondChildsFirstChildId);
    }
}
