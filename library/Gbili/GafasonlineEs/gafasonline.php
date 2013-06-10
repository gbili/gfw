<?php
use Gbili\Regex\String\Generic;

use Gbili\Miner\Blueprint\Savable\Wrapper;
use Gbili\GafasonlineEs\Lexer;

$a = new Wrapper('gafasonline.es');

$a->createChildGetContents()
    ->setTitle('1.1. Go to Home Page')
    ->setData('http://gafasonline.es');

//
$a->createChildExtract()
    ->setTitle('2.1. Extract brands from home page')
    ->setUseMatchAll(true)
    ->spitGroupAsEntity('brandName', Lexer::BRAND_NAME)
    ->spitGroupAsEntity('brandThumb', Lexer::BRAND_THUMB)
    ->spitGroupAsEntity('brandModelsUrl', Lexer::BRAND_URL)
    ->setData('<div class="elementoSeccionMarca"><div class="styleSeccionImage"><a href="(?P<brandModelsUrl>[^"]+)" title="[^"]+"><img src="(?P<brandThumb>[^"]+)" border="0" alt="(?P<brandName>[^"]+)"></a></div></div>');

//CATEGORY and page
$a->createChildGetContents()
    ->setTitle('4.1. Go to brand  page')
    ->setInputParentRegexGroup('brandModelsUrl');

//CATEGORIES
$a->createChildExtract()
    ->setTitle('3.1. Extract each model from brand page')
    ->setUseMatchAll(true)
    ->spitGroupAsEntity('modelName', Lexer::MODEL_NAME)
    ->spitGroupAsEntity('modelUrl', Lexer::MODEL_URL)
    ->spitGroupAsEntity('modelThumb', Lexer::MODEL_THUMB)
    ->setData('<div class="elementoSeccion"><div class="styleSeccionImage"><a href="[^"]+" title="[^"]+"><img src="(?P<modelThumb>[^"]+)" border="0" alt="[^"]+"></a></div><div class="styleSeccionName"><a href="(?P<modelUrl>[^"]+)">(?P<modelName>[^<]+)</a></div></div>');

//CATEGORY and page
$a->createChildGetContents()
    ->setTitle('4.1. Go to model page')
    ->setInputParentRegexGroup('modelUrl');

//Optional host name
$a->createChildExtract()
    ->setTitle('6.2. Extract model Type')
    ->setUseMatchAll(false)
    ->setAsOptional()
    ->spitGroupAsEntity('productType', Lexer::MODEL_TYPE)
    ->setData('<tr>[^<]*<td align="left" colspan="2" class="category_desc">(?:<span><span>)?(?:(?:Colecci&oacute;n)|(?:Collection)): (?:</span><span>)?(?P<productType>[^<]+)');

//Optional host name
$a->createBrotherExtract()
    ->setTitle('6.2. Extract model general Material')
    ->setUseMatchAll(false)
    ->setAsOptional()
    ->spitGroupAsEntity('productMaterial', Lexer::MODEL_MATERIAL)
    ->spitGroupAsEntity('gender', Lexer::MODEL_GENDER)
    ->setData('<br />Mat(?:(?:&eacute;)|e)ri[ae]l: (?P<productMaterial>[^<]+)<br />(?:G&eacute;nero)|(?:Gender)|(?:Sexe): (?P<gender>[^<]+)</td>');

//extract products
$a->createBrotherExtract()
    ->setTitle('5.1. Extract each product from model page')
    ->setUseMatchAll(true)
    ->spitGroupAsEntity('productUrl'     , Lexer::SUBMODEL_URL)
	->spitGroupAsEntity('productThumb'  , Lexer::SUBMODEL_THUMB)
	->spitGroupAsEntity('productName'  , Lexer::SUBMODEL_NAME)
	->spitGroupAsEntity('productPrice'  , Lexer::SUBMODEL_PRICE)
	->spitGroupAsEntity('productPriceCurrency'  , Lexer::SUBMODEL_PRICE_CURRENCY)
	->spitGroupAsEntity('productDiscounted'  , Lexer::SUBMODEL_PRICE_DISCOUNTED)
	->spitGroupAsEntity('discountedCurrency'  , Lexer::SUBMODEL_PRICE_DISCOUNTED_CURRENCY)
	->setAsNewInstanceGeneratingPoint()
    ->setData('<span align="center" class="styleProductImage">&nbsp;<a href="(?P<productUrl>[^"]+)"><img src="(?P<productThumb>[^"]+)" border="0" alt="[^"]+"></a>&nbsp;</span>[^<]*<span class="styleProductName"><a href="[^"]+">(?:(?:[^<]+<br />)?[^<]+<br />)?[^<]+<br />(?P<productName>[^<]+)</a>&nbsp;</span>[^<]*<span align="right" class="styleProductPrice"><s>(?P<productPrice>[0-9]+\.[0-9]+)(?P<priceCurrency>[^<]+)</s>&nbsp;&nbsp;<span class="productSpecialPrice">(?P<productDiscounted>[0-9]+\.[0-9]+)(?P<discountedCurrency>[^<]+)');

//CATEGORY and page
$a->createChildGetContents()
    ->setTitle('4.1. Go to product page')
    ->setInputParentRegexGroup('productUrl');

//VIDEO SOURCE
$a->createChildExtract()
    ->setTitle('6.1. extract product picture')
    ->setUseMatchAll(false)
    ->spitGroupAsEntity('productPicture', Lexer::SUBMODEL_PICTURE)
    ->setData('<td class="main">[^<]*<div style="text-align:center;width:500px;">[^<]*<img src="(?P<productPicture>[^"]+)" width="450"');

//Optional host name
$a->createBrotherExtract()
    ->setTitle('6.2. Extract optional calibre')
    ->setAsOptional()
    ->setUseMatchAll(false)
    ->spitGroupAsEntity('submodelCalibre', Lexer::SUBMODEL_CALIBRE)
    ->setData('<td class="main">Calibre:</td>[^<]*<td class="main">(?P<productCalibre>[^<]+)<input type="hidden" name="id\[1\]" value="[^"]+"></td>');

$a->getBlueprint()->save();

echo 'It looks like installation went pretty well!';
