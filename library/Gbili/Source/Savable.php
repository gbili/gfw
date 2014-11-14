<?php
namespace Gbili\Source;

use Gbili\Url\Url;
use Gbili\Source\Host\Savable as SavableHost;

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
	 * @param Url $url
	 * @return unknown_type
	 */
	public function __construct(Url $url)
	{
		parent::__construct();
		$this->setPassTableNameToRequestor();
		$this->setElement('host', new SavableHost($url->getHost()));
		$this->setElement('path', $url->getPath());
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getPath()
	{
		return $this->getElement('path');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getHost()
	{
		return $this->getElement('host');
	}
}