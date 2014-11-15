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
     * Tells whether the contents have been
     * fetched from storage or through the
     * built in function file_get_contents
     *
     * @var boolan
     */
    protected $isContentsFromStorage;

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
            $contents = $this->getFreshContents();
        } else {
            $this->isContentsFromStorage = true;
        }

	    if (false !== $contents) {
	        $this->setContents($contents);
	    }
	    
	    return $contents;
	}

	/**
	 * Get contents from builtin function not from db
     *
	 * @return mixed:false|string 
	 */
	public function getFreshContents()
	{
        $result = file_get_contents($url->toString());
        $this->isContentsFromStorage = false;
        if (false !== $result) {
    	    $result = \Gbili\Encoding\Encoding::utf8Encode($result);
        }
	    return $result;
	}

    /**
     * Have the contents been fetched from the
     * built in method file_get_contents just now
     * or do they come from storage
     * @return boolean
     */
    public function isFreshContents()
    {
        if (null === $this->isContentsFromStorage) {
            throw new \Exception('Call getContents() before ' . __FUNCTION__);
        }
        return !$this->isContentsFromStorage;
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
