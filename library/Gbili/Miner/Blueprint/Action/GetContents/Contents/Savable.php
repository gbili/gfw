<?php
namespace Gbili\Miner\Blueprint\Action\GetContents\Contents;

/**
 * 
 * @author gui
 *
 */
class Savable
extends \Gbili\Savable\Savable
implements \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherInterface
{
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
	    if (!$this->hasContents()) {
            $this->fetchContents();
	    }
        return $this->getElement('contents');
	}

    /**
     * Gets the contents from 
     * @return self
     */
    public function fetch(\Gbili\Url\UrlInterface $url)
    {
        $this->setUrl($url);
        if ($contents = \Gbili\Db\Registry::getInstance($this)->getContents($this->getUrl())) {
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
