<?php
namespace Gbili\Source\Host;

use Gbili\Url\Authority\Host;

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
	 * @param Url_Authority_Host $host
	 * @return unknown_type
	 */
	public function __construct(Host $host)
	{
		parent::__construct();
		$this->setPassTableNameToRequestor();
		$this->setElement('host', $host);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getHost()
	{
		return $this->getElement('host');
	}
	
	/**
	 * Human friendly name
	 * @param unknown_type $humanFriendlyName
	 * @return unknown_type
	 */
	public function setUserFriendlyName($name)
	{
		$this->setElement('hFName', (string) $name);
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function hasUserFriendlyName()
	{
	    return $this->isSetKey('hFName');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getUserFriendlyName()
	{
		return $this->getElement('hFName');
	}
}