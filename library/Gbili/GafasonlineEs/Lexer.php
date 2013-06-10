<?php
namespace Gbili\GafasonlineEs;

use Gbili\Miner\Lexer\AbstractLexer;

/**
 * This will populate the video entites
 * with the data in the way specified in populateInstance();
 * @author gui
 *
 */
class Lexer
extends AbstractLexer
{
	
	/**
	 * 
	 * @var unknown_type
	 */
    const BRAND_NAME     = 21;
    const BRAND_URL      = 22;
    const BRAND_THUMB    = 23;

    const MODEL_NAME     = 11;
    const MODEL_THUMB    = 12;
    const MODEL_URL      = 13;

    const MODEL_TYPE     = 14;
    const MODEL_MATERIAL = 15;
    const MODEL_GENDER   = 16;

    const SUBMODEL_URL                       = 1;
    const SUBMODEL_THUMB                     = 2;
    const SUBMODEL_NAME                      = 3;
    const SUBMODEL_PRICE                     = 4;
    const SUBMODEL_PRICE_CURRENCY            = 5;
    const SUBMODEL_PRICE_DISCOUNTED          = 6;
    const SUBMODEL_PRICE_DISCOUNTED_CURRENCY = 7;
    const SUBMODEL_PICTURE                   = 8;
    const SUBMODEL_CALIBRE                   = 9;

    protected $brandOpened   = false;
    protected $modelOpened   = false;
    protected $productOpened = false;
    protected $priceOpened = false;
	/**
	 * 
	 * @param array $info
	 * @return unknown_type
	 */
	public function populateInstance($instance, array $info)
	{
	    ob_start();
		foreach ($info as $entity => $value) {
            switch ($entity) {
                case self::BRAND_NAME:
                    echo "<name>$value</name>\n";
                    break;
                case self::BRAND_URL:
                    if ($this->productOpened) {
                        echo "</product>\n";
                        $this->productOpened = false;
                    }
                    if ($this->modelOpened) {
                        echo "</model>\n";
                        $this->modelOpened = false;
                    }
                    if ($this->brandOpened) {
                        echo "</brand>\n";
                        $this->brandOpened = false;
                    }
                    echo "<brand>\n";
                    $this->brandOpened = true;
                    echo "<url>$value</url>\n";
                    break;
                case self::BRAND_THUMB:
                    echo "<thumb>http://gafasonline.es/$value</thumb>\n";
                    break;
                case self::MODEL_NAME:
                    if ($this->productOpened) {
                        echo "</product>\n";
                        $this->productOpened = false;
                    }
                    if ($this->modelOpened) {
                        echo "</model>\n";
                        $this->modelOpened = false;
                    }
                    echo "<model>\n";
                    $this->modelOpened = true;
                    echo "<name>$value</name>\n";
                    break;
                case self::MODEL_THUMB:
                    echo "<thumb>http://gafasonline.es/$value</thumb>\n";
                    break;
                case self::MODEL_URL:
                    echo "<url>$value</url>\n";
                    break;
                case self::MODEL_TYPE:
                    echo "<type>$value</type>\n";
                    break;
                case self::MODEL_MATERIAL:
                    echo "<material>$value</material>\n";
                    break;
                case self::MODEL_GENDER:
                    echo "<gender>$value</gender>\n";
                    break;
                case self::SUBMODEL_URL:
                    echo "<SUBMODEL_URL>$value</SUBMODEL_URL>\n";
                    break;
                case self::SUBMODEL_THUMB:
                    echo "<SUBMODEL_THUMB>$value</SUBMODEL_THUMB>\n";
                    break;
                case self::SUBMODEL_NAME:
                    echo "<SUBMODEL_NAME>$value</SUBMODEL_NAME>\n";
                    break;
                case self::SUBMODEL_PRICE:
                    if ($this->productOpened) {
                        echo "</product>\n";
                        $this->productOpened = false;
                    }
                    echo "<product>\n";
                    $this->productOpened = true;
                    echo "<price>\n";
                    $this->priceOpened = true;
                    if ($this->priceOpened) {
                        echo "</price>\n";
                        $this->priceOpened = false;
                    }
                    echo "<value>$value</value>\n";
                    break;
                case self::SUBMODEL_PRICE_CURRENCY:
                    echo "<currency>$value</currency>\n";
                    break;
                case self::SUBMODEL_PRICE_DISCOUNTED:
                    echo "<discounted_value>$value</discounted_value>\n";
                    break;
                case self::SUBMODEL_PRICE_DISCOUNTED_CURRENCY:
                    echo "<discounted_currency>$value</discounted_currency>\n";
                    break;
                case self::SUBMODEL_PICTURE:
                    echo "<SUBMODEL_PICTURE>$value</SUBMODEL_PICTURE>\n";
                    break;
                case self::SUBMODEL_CALIBRE:
                    echo "<SUBMODEL_CALIBRE>$value</SUBMODEL_CALIBRE>\n";
                    break;
				default;
					throw new Exception('The info array passed to populate instance apears not to be compliant' . print_r($info, true));
				break;
			}
		}
		$out = ob_get_flush();
		$fileContents = file_get_contents('/var/www/Tests/document_root/gafasonlinees.xml');
		file_put_contents('/var/www/Tests/document_root/gafasonlinees.xml', $fileContents . $out);
	}
	
	public function xml()
	{
        // THIS IS ABSOLUTELY ESSENTIAL - DO NOT FORGET TO SET THIS 
        @date_default_timezone_set("GMT"); 
        
        $writer = new XMLWriter(); 
        // Output directly to the user 
        
        //$writer->openURI('php://output'); 
        $writer->startDocument('1.0'); 
        
        $writer->setIndent(4); 
        
        // declare it as an rss document 
        $writer->startElement('rss'); 
        $writer->writeAttribute('version', '2.0'); 
        $writer->writeAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom'); 
        
        
        $writer->startElement("channel"); 
        //---------------------------------------------------- 
        //$writer->writeElement('ttl', '0'); 
        $writer->writeElement('title', 'Latest Products'); 
        $writer->writeElement('description', 'This is the latest products from our website.'); 
        $writer->writeElement('link', 'http://www.domain.com/link.htm'); 
        $writer->writeElement('pubDate', date("D, d M Y H:i:s e")); 
            $writer->startElement('image'); 
                $writer->writeElement('title', 'Latest Products'); 
                $writer->writeElement('link', 'http://www.domain.com/link.htm'); 
                $writer->writeElement('url', 'http://www.iab.net/media/image/120x60.gif'); 
                $writer->writeElement('width', '120'); 
                $writer->writeElement('height', '60'); 
            $writer->endElement(); 
        //---------------------------------------------------- 
        
        
        
        //---------------------------------------------------- 
        $writer->startElement("item"); 
        $writer->writeElement('title', 'New Product 8'); 
        $writer->writeElement('link', 'http://www.domain.com/link.htm'); 
        $writer->writeElement('description', 'Description 8 Yeah!'); 
        $writer->writeElement('guid', 'http://www.domain.com/link.htm?tiem=1234'); 
        
        $writer->writeElement('pubDate', date("D, d M Y H:i:s e")); 
        
        $writer->startElement('category'); 
            $writer->writeAttribute('domain', 'http://www.domain.com/link.htm'); 
            $writer->text('May 2008'); 
        $writer->endElement(); // Category 
        
        // End Item 
        $writer->endElement(); 
        //---------------------------------------------------- 
        
        
        // End channel 
        $writer->endElement(); 
        
        // End rss 
        $writer->endElement(); 
        
        $writer->endDocument(); 
        
        $writer->flush(); 
    }
}
