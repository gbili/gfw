<?php
namespace Gbili\Miner\Blueprint\Action\GetContents\Contents;

/**
 * 
 * @author gui
 *
 */
class Web 
implements \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherInterface
{
    /**
     * Gets the contents from 
     * @return self
     */
    public function fetch(\Gbili\Url\UrlInterface $url)
    {
        $this->setUrl($url);
        $result = file_get_contents($this->getUrl()->toString());
        if (false !== $result) {
    	    $result = \Gbili\Encoding\Encoding::utf8Encode($result);
        }
        return $result;
    }
}
