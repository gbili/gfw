<?php
namespace Gbili\Miner\Blueprint\Action\GetContents\Contents;

/**
 * 
 * @author gui
 *
 */
class Savable
extends \Gbili\Savable\Savable
implements 
    \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherInterface,
    \Gbili\EventManager\AttachToEventManagerInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler
     */
    protected $callbackHandler;

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
        if (!$this->isSetKey('url')) {
            if ($this->getElement('url') !== $url) { // bug with || and && unexpected variable or call to undefined ()
                return $this;
            }
        }
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
        if (!$this->isSetKey('contents') || ($this->getElement('contents') !== $contents)) {
            $this->setElement('contents', $contents);
        }
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
            $this->fetch($this->getUrl());
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
        $contents = false;
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

    /**
     * @see A
     */
    public function attachToEventManager(\Zend\EventManager\EventManagerInterface $em)
    {
        $this->callbackHandler = $em->attach(
            'fetch.hasResult',
            function ($e) { // Save contents to database when they are obtained elsewhere
                $params = $e->getParams();
                if ($params['fetcher'] === $this) {
                    return false;
                }
                $this->setId(null); // make a new instance
                $this->setUrl($params['url']);
                $this->setContents($params['content']);
                $this->save();
                return true;
            }
        );
    }

    public function detachFromEventManager(\Zend\EventManager\EventManagerInterface $em)
    {
        if (null !== $this->callbackHandler) {
            $em->detach($this->callbackHandler);
            $this->callbackHandler = null;
        }
    }
}
