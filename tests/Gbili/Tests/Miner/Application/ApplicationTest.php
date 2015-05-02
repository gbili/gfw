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
            'blueprint_type'              => 'db_req',
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

    protected function installActionSet()
    {
        $a = new \Gbili\Miner\Blueprint\Savable\Wrapper('shopstarbuzz.com');
        $a->createChildGetContents()
            ->setTitle('1.1. Go to products page')
            ->setData('http://www.shopstarbuzz.com/starbuzz/?sort=featured&page=1');

        $a->createChildExtract()
            ->setTitle('2.1. Extract products list from page')
            ->setUseMatchAll(false)
            ->setData('<ul class="ProductList[^>]+?>.+?</ul>');

        $a->createChildExtract()
            ->setTitle('Get each product list item')
            ->setUseMatchAll(true)
            ->setData('<li class=".+?</li>');

        $a->createChildExtract()
            ->setTitle('extract each product thumbnail')
            ->setUseMatchAll(false)
            ->spitGroupAsEntity('tumbnailAlt', 'IMG_THUMB_ALT')
            ->spitGroupAsEntity('tumbnailSrc', 'IMG_THUMB_SRC')
            ->setAsNewInstanceGeneratingPoint()
            ->setData('<img src="(?P<thumbnailSrc>[^"]+?)" alt="(?P<thumbnailAlt>[^"]+?)"');

        $a->createBrotherExtract()
            ->setTitle('extract each product details url and name')
            ->setUseMatchAll(false)
            ->spitGroupAsEntity('name', 'NAME')
            ->spitGroupAsEntity('productUrl', 'URL')
            ->setData('<div class="ProductDetails">[^<]+?<strong><a href="(?P<productUrl>[^"]+?)" class="">(?P<name>[^<]+)</a></strong>');

        $prodAction = $a->createChildGetContents()
            ->setTitle('2 Go to produtct  page')
            ->setInputParentRegexGroup('productUrl');

        // Product images
        $a->createChildExtract($prodAction)
            ->setTitle('2.1 extract product images section')
            ->setUseMatchAll(false)
            ->setData('<div class="ProductThumbImage" style="[^"]+?">.+?</div>');

        $a->createChildExtract()
            ->setTitle('2.1.1 extract big image src')
            ->setUseMatchAll(false)
            ->spitGroupAsEntity('productImgBigSrc', 'IMG_BIG_SRC')
            ->setData('href="(?P<productImgBigSrc>.+?)\\?c=2"');

        $a->createBrotherExtract()
            ->setTitle('2.1.2 extract image alt')
            ->setUseMatchAll(false)
            ->spitGroupAsEntity('productImgAlt', 'IMG_ALT')
            ->setData('alt="(?P<productImgAlt>[^"]+?)"');

        $a->createBrotherExtract()
            ->setTitle('2.1.3 extract medium image src')
            ->setUseMatchAll(false)
            ->spitGroupAsEntity('productImgMedSrc', 'IMG_MED_SRC')
            ->setData('src="(?P<productImgMedSrc>[^"]+?)\\?c=2"');

        $a->createChildExtract($prodAction)
            ->setTitle('2.2 extract product title')
            ->setUseMatchAll(false)
            ->spitGroupAsEntity('productTitle', 'TITLE')
            ->setData('<h1>(?P<productTitle>[^<]+?)</h1>');

        $a->createBrotherExtract()
            ->setTitle('2.3 extract product price')
            ->setUseMatchAll(false)
            ->spitGroupAsEntity('productPrice',  'PRICE')
            ->setData('<em class="ProductPrice VariationProductPrice">(?P<productPrice>[^<]+?)</em>');

        /*
        $a->createBrotherExtract()
            ->setTitle('2.4 extract product weight')
            ->setUseMatchAll(false)
            ->spitGroupAsEntity('productWeight', 'WEIGHT')
            ->spitGroupAsEntity('productWeightUnit', Lexer::MODEL_NAME)
            ->setData('<span class="VariationProductWeight">[^0-9]+?(?P<productWeight>\\S+?)\\s(?P<productWeightUnit>[A-Z]+?)[^<]+?</span>');
        */

        $a->createBrotherExtract()
            ->setTitle('2.5 extract product selected price weight')
            ->setUseMatchAll(false)
            ->spitGroupAsEntity('productSelectedWeight', 'WEIGHT')
            ->spitGroupAsEntity('productSelectedWeightUnit', 'WEIGHT_UNIT')
            ->setData('<input[^a-z]+?type="radio".+?checked="checked"[^>]+?>[^<]+?<span [^>]+?>(?P<productSelectedWeight>[0-9]+?)(?P<productSelectedWeightUnit>[a-z]+?)</span>');

        $a->createBrotherExtract()
            ->setTitle('2.6 extract product description')
            ->setUseMatchAll(false)
            ->spitGroupAsEntity('productDescription', 'DESCRIPTION')
        ->setData('<div class="ProductDescriptionContainer prodAccordionContent">[^<]+?<p><span style="font-size: small;">(?P<productDescrption>[^<]+?)</span></p>[^<]+?</div>');

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
      //  $this->app->run();
    }
}
