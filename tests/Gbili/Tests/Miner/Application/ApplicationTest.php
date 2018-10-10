<?php
namespace Gbili\Tests\Miner\Application;

class ApplicationTest extends \Gbili\Tests\GbiliTestCase
{
    protected function initDbReq()
    {
        $pdo = $this->getPDO();
        \Gbili\Db\Req\AbstractReq::setAdapter($pdo);
        \Gbili\Db\Registry::setReqClassNameGenerator(\Gbili\Db\Registry::getDefaultReqClassNameGenerator());
    }

    protected function getPDO()
    {
        $dSN = "mysql:host=127.0.0.1;dbname=miner";
        $mysqlUser = 'g';
        return new \PDO($dSN, $mysqlUser, 'mysql', array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    }

    protected function installDbTables()
    {
        $this->installer = new \Gbili\Miner\Installer;
        $this->installer->setTableSchemaPath(__DIR__ . '/../../../../../boot/conf/db/tables_dumperengine.sql');
        $this->installer->deleteExisting(true);
        $this->installer->install();
    }

    protected function initApp()
    {
        $config = array(
            'contents_fetcher_aggregate' => array(
                'queue' => array(
                    10 => array(
                        new \Gbili\Tests\Miner\Blueprint\Action\GetContents\Contents\MockContentsFetcherWebPage,
                        new \Gbili\Tests\Miner\Blueprint\Action\GetContents\Contents\MockContentsFetcherLink,
                    ),
                ),
            ),
            'blueprint'                   => array(
                'type' => 'db_req',
            ),
            'host'                        => 'http://somedomain.com',
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
                    'Lexer' => '\Gbili\Tests\Miner\SomeLexer',
                 )
            ),
        );
        $this->app = \Gbili\Miner\Application\Application::init($config);
    }

    protected function installActionSet()
    {
        $a = new \Gbili\Miner\Blueprint\Savable\Wrapper('http://somedomain.com');
        $a->createChildGetContents()
            ->setTitle($this->rootActionId)
            ->setData('http://somedomain.com');

        $a->createChildExtract()
            ->setTitle($this->rootFirstChildId)
            ->setUseMatchAll(false)
            ->setData('#<title>(?P<title>[^<]+)</title>#ig')
            ->spitGroupAsEntity('title', 'TITLE');

        $some = $a->createBrotherExtract() //$some is not necessary
            ->setTitle($this->rootSecondChildId)
            ->setUseMatchAll(true)
            ->setData('#<a.+?href="(?P<link>[^"]+)"#ig')
            ->spitGroupAsEntity('link', 'LINK');

        $a->createChildGetContents($some) //$some is not necessary
            ->setTitle($this->rootSecondChildsFirstChildId)
            ->setInputParentRegexGroup('link')
            ->setAsNewInstanceGeneratingPoint();

        $a->save();
    }

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

        $this->initDbReq();
        $this->installDbTables();
        $this->installActionSet();
        $this->initApp();
    }

    public function testAppInitReturnsAppInstance()
    {
        $this->assertEquals($this->app instanceof \Gbili\Miner\Application\Application, true);
    }

    /**
     * Tears down the fixture, for example, close a network connection
     * This method is called after a test is executed
     *
     * @return void
     */
    public function tearDown()
    {
        //$this->installer->uninstall(true);
        \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable::clearOrder();
    }

    public function testAppCanBeRun()
    {
        $this->app->run();
    }
}
