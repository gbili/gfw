<?php
namespace Gbili\Tests\Miner\Blueprint\Savable;

class WrapperTest// extends \Gbili\Tests\GbiliTestCase
{
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
        $this->wrapper = new \Gbili\Miner\Blueprint\Savable\Wrapper('gafasonline.es');
    }

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
        $this->installer->setTableSchemaPath(__DIR__ . '/../../../../../../boot/conf/db/tables_dumperengine.sql');
        $this->installer->deleteExisting(true);
        $works = $this->installer->install();
    }

    /**
     * Tears down the fixture, for example, close a network connection
     * This method is called after a test is executed
     *
     * @return void
     */
    public function tearDown()
    {
        $this->cleanBehind();
    }

    protected function cleanBehind()
    {
        //remove all tables
        $this->installer->uninstall(true);
        //For every new action set, the static order info has to be cleared
        \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable::clearOrder();
    }

    public function testCanCreateRootActionGetContentsWithCallback()
    {
        $a = $this->wrapper;
        $a->createChildGetContents()
            ->setTitle('1.1. Go to Home Page')
            ->setData('http://gafasonline.es')
            ->setCallable(array('\My\Namespace\SomeClass', 'methodNameWithTwoParams'));
        $a->save();
    }

    public function testCanCreateActionGetContentsWithCallbackAndCallbackMap()
    {
        $a = $this->wrapper;
        $a->createChildGetContents()
            ->setTitle('1.1. Go to Home Page')
            ->setData('http://gafasonline.es');
        $a->createChildExtract()
            ->setTitle('2.1. extract url parts')
            ->setUseMatchAll(true)
            ->spitGroupAsEntity('brandName', 'BRAND_NAME')
            ->spitGroupAsEntity('brandThumb', 'BRAND_THUMB')
            ->spitGroupAsEntity('brandModelsUrl', 'BRAND_URL')
            ->setData('<div class="elementoSeccionMarca"><div class="styleSeccionImage">(?P<schema>[^<]+"<div title="[^"]+"><img src="(?P<path>[^"]+)" border="0"></a></div></div><p name="domain">(?P<domain>[\\w.]+)</p>');
        $a->createChildGetContents()
            ->setTitle('1.1. Go to somewhere else')
            ->setData('http://gafasonline.es')
            ->setCallable(array('\My\Namespace\SomeClass', 'createAValidUrlFromParts'))
            ->setCallbackMap(array(0=>'schema', 1=>'domain', 2=>'path'));
        $a->save();
    }

    public function testCanCreateActionSet()
    {
        $a = $this->wrapper;
        $a->createChildGetContents()
            ->setTitle('1.1. Go to Home Page')
            ->setData('http://gafasonline.es');

        $a->createChildExtract()
            ->setTitle('2.1. Extract brands from home page')
            ->setUseMatchAll(true)
            ->spitGroupAsEntity('brandName', 'BRAND_NAME')
            ->spitGroupAsEntity('brandThumb', 'BRAND_THUMB')
            ->spitGroupAsEntity('brandModelsUrl', 'BRAND_URL')
            ->setData('<div class="elementoSeccionMarca"><div class="styleSeccionImage"><a href="(?P<brandModelsUrl>[^\\?"]+)(?:[^"]*)" title="[^"]+"><img src="(?P<brandThumb>[^"]+)" border="0" alt="(?P<brandName>[^"]+)"></a></div></div>');

        //CATEGORY and page
        $a->createChildGetContents()
            ->setTitle('4.1. Go to brand  page')
            ->setInputParentRegexGroup('brandModelsUrl');

        //CATEGORIES
        $a->createChildExtract()
            ->setTitle('3.1. Extract each model from brand page')
            ->setUseMatchAll(true)
            ->spitGroupAsEntity('modelName', 'MODEL_NAME')
            ->spitGroupAsEntity('modelUrl', 'MODEL_URL')
            ->spitGroupAsEntity('modelThumb', 'MODEL_THUMB')
            ->setData('<div class="elementoSeccion"><div class="styleSeccionImage"><a href="[^"]+" title="[^"]+"><img src="(?P<modelThumb>[^"]+)" border="0" alt="[^"]+"></a></div><div class="styleSeccionName"><a href="(?P<modelUrl>[^\\?"]+)(?:[^"]*)">(?P<modelName>[^<]+)</a></div></div>');

        //CATEGORY and page
        $a->createChildGetContents()
            ->setTitle('4.1. Go to model page')
            ->setInputParentRegexGroup('modelUrl');

        //Optional host name
        $a->createChildExtract()
            ->setTitle('6.2. Extract model Type')
            ->setUseMatchAll(false)
            ->setAsOptional()
            ->spitGroupAsEntity('productType', 'MODEL_TYPE')
            ->setData('<tr>[^<]*<td align="left" colspan="2" class="category_desc">(?:<span><span>)?(?:(?:Colecci&oacute;n)|(?:Collection)): (?:</span><span>)?(?P<productType>[^<]+)');

        //Optional host name
        $a->createBrotherExtract()
            ->setTitle('6.2. Extract model general Material')
            ->setUseMatchAll(false)
            ->setAsOptional()
            ->spitGroupAsEntity('productMaterial', 'MODEL_MATERIAL')
            ->spitGroupAsEntity('gender', 'MODEL_GENDER')
            ->setData('<br />Mat(?:(?:&eacute;)|e)ri[ae]l: (?P<productMaterial>[^<]+)<br />(?:G&eacute;nero)|(?:Gender)|(?:Sexe): (?P<gender>[^<]+)</td>');

        //extract products
        $a->createBrotherExtract()
            ->setTitle('5.1. Extract each product from model page')
            ->setUseMatchAll(true)
            ->spitGroupAsEntity('productUrl'     , 'SUBMODEL_URL')
            ->spitGroupAsEntity('productThumb'  , 'SUBMODEL_THUMB')
            ->spitGroupAsEntity('productName'  , 'SUBMODEL_NAME')
            ->spitGroupAsEntity('productPrice'  , 'SUBMODEL_PRICE')
            ->spitGroupAsEntity('productPriceCurrency'  , 'SUBMODEL_PRICE_CURRENCY')
            ->spitGroupAsEntity('productDiscounted'  , 'SUBMODEL_PRICE_DISCOUNTED')
            ->spitGroupAsEntity('discountedCurrency'  , 'SUBMODEL_PRICE_DISCOUNTED_CURRENCY')
            ->setAsNewInstanceGeneratingPoint()
            ->setData('<span align="center" class="styleProductImage">&nbsp;<a href="(?P<productUrl>[^\\?"]+)(?:[^"]*)"><img src="(?P<productThumb>[^"]+)" border="0" alt="[^"]+"></a>&nbsp;</span>[^<]*<span class="styleProductName"><a href="[^"]+">(?:(?:[^<]+<br />)?[^<]+<br />)?[^<]+<br />(?P<productName>[^<]+)</a>&nbsp;</span>[^<]*<span align="right" class="styleProductPrice"><s>(?P<productPrice>[0-9]+\.[0-9]+)(?P<priceCurrency>[^<]+)</s>&nbsp;&nbsp;<span class="productSpecialPrice">(?P<productDiscounted>[0-9]+\.[0-9]+)(?P<discountedCurrency>[^<]+)');

        //CATEGORY and page
        $a->createChildGetContents()
            ->setTitle('4.1. Go to product page')
            ->setInputParentRegexGroup('productUrl');

        //VIDEO SOURCE
        $a->createChildExtract()
            ->setTitle('6.1. extract product picture')
            ->setUseMatchAll(false)
            ->spitGroupAsEntity('productPicture', 'SUBMODEL_PICTURE')
            ->setData('<td class="main">[^<]*<div style="text-align:center;width:500px;">[^<]*<img src="(?P<productPicture>[^"]+)" width="450"');

        //Optional host name
        $a->createBrotherExtract()
            ->setTitle('6.2. Extract optional calibre')
            ->setAsOptional()
            ->setUseMatchAll(false)
            ->spitGroupAsEntity('submodelCalibre', 'SUBMODEL_CALIBRE')
            ->setData('<td class="main">Calibre:</td>[^<]*<td class="main">(?P<productCalibre>[^<]+)<input type="hidden" name="id\[1\]" value="[^"]+"></td>');
        $this->wrapper->getBlueprint()->save();
    }
}
