<?php
namespace Gbili\Url;

use Gbili\Regex\Encapsulator\AbstractEncapsulator;

/**
 * Valid url placeholder it will also divide the url
 * in a logical placeholder way : <scheme><subdomains><authority><path>
 * <url> : http://videos.spain.megaupload.com/path?to=file/?path
 * <scheme> : http
 * <subdomains> : videos.spain
 * <authority> : megaupload.com
 * <path> : /path?to=file/?path
 * 
 * for this version only full url are allowed.
 * there must_ be a scheme and authority
 * 
 * @author gui
 *
 */
class Url
extends AbstractEncapsulator
{
	
	/**
	 * (non-PHPdoc)
	 * @see Gbili\Regex\Encapsulator.AbstractEncapsulator::partsToString()
	 */
	protected function partsToString()
	{
		return $this->getScheme() . '://' . $this->getAuthority()->toString() . (($this->hasPath())? $this->getPath() : '');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasScheme()
	{
		return $this->hasPart('Scheme');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getScheme()
	{
		return $this->getPart('Scheme');
	}
	
	/**
	 * 
	 * @param unknown_type $scheme
	 * @return unknown_type
	 */
	public function setScheme($scheme)
	{
		$this->setPartWithDirtyData('Scheme', $scheme, false);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getAuthority()
	{
		return $this->getPart('Authority');
	}
	
	/**
	 * 
	 * @param unknown_type $authority
	 * @return unknown_type
	 */
	public function setAuthority($authority)
	{
		$this->setPartWithDirtyData('Authority', $authority);
	}
	
	/**
	 * Proxy
	 * 
	 * @return unknown_type
	 */
	public function getHost()
	{
		return $this->getAuthority()->getHost();
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasPath()
	{
		return $this->hasPart('Path');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getPath()
	{
		return $this->getPart('Path');
	}

	/**
	 * 
	 * @param unknown_type $path
	 * @return unknown_type
	 */
	public function setPath($path)
	{
		//the path must start with /
		if ('/' !== mb_substr($path, 0, 1)) {
			$path = '/' . $path;
		}
		$this->setPartWithDirtyData('Path', $path, false);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Url/G_Url_Abstract#_setParts()
	 */
	protected function setParts()
	{
		$this->setPart('Scheme', (($this->hasScheme())? $this->regex->getScheme() : 'http'), false);
		$this->setPart('Authority', $this->regex->getAuthority());
		if ($this->getRegex()->hasPath()) {
			$this->setPart('Path', $this->regex->getPath(), false);
		}
		
	}
}