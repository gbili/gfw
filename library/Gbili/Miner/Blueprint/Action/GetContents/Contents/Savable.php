<?php
namespace Gbili\Miner\Blueprint\Action\GetContents\Contents;

/**
 * 
 * @author gui
 *
 */
class Savable
extends \Gbili\Savable\Savable
{
	/**
	 * 
	 * @return unknown_type
	 */	
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasUrl()
	{
		return $this->isSetKey('url');
	}
	
	/**
	 * 
	 * @param unknown_type $methodName
	 * @throws Exception
	 * @return \Gbili\Miner\Blueprint\Action\GetContents\Savable
	 */
	public function setUrl(\Gbili\Url\Url $url)
	{
		$this->setElement('url', $url);
		return $this;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getUrl()
	{
		return $this->getElement('url');
	}
	
	/**
	 * 
	 * @param array $mapping
	 * @throws Exception
	 * @return \Gbili\Miner\Blueprint\Action\GetContents\Savable
	 */
	public function setContents($contents)
	{
		$this->setElement('contents', $contents);
		return $this;
	}
	
	/**
	 * Try to get contents from db or web, if
	 * success set contents in instance, or just
	 * return $contents containing false
	 * 
	 * @return string|booleanl
	 */
	public function getContents()
	{
	    if ($this->hasContents()) {
	        return $this->getElement('contents');
	    }
	    
	    if (!$this->hasUrl()) {
	        throw new \Exception('In order to get contents you must set the url');
	    }
	    
	    $contents = \Gbili\Db\Registry::getInstance($this)->getContents($this->getUrl());

        if (false === $contents) {
            $contents = file_get_contents($this->getUrl()->toString());
        }
	    
	    if (false !== $contents) {
	        $this->setContents($contents);
	    }
	    
	    return $contents;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasContents()
	{
		return $this->isSetKey('contents');
	}
}
