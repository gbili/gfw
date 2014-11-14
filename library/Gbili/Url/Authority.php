<?php
namespace Gbili\Url;

use Gbili\Regex\Encapsulator\AbstractEncapsulator;

class Authority
extends AbstractEncapsulator
{
    /**
     * (non-PHPdoc)
     * @see Gbili\Regex\Encapsulator.AbstractEncapsulator::partsToString()
     */
	protected function partsToString()
	{
		return (($this->hasUserInfo())? $this->getUserInfo()->toString() : '') . $this->getHost()->toString() . (($this->hasPort())? ':' . $this->getPort() : '');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasUserInfo()
	{
		return $this->hasPart('UserInfo');
	}
	
	/**
	 * 
	 * @param $userInfo
	 * @return unknown_type
	 */
	public function setUserInfo($userInfo)
	{
		$this->setPartWithDirtyData('UserInfo', $userInfo);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getUserInfo()
	{
		return $this->getPart('UserInfo');		
	}
	
	/**
	 * 
	 * @param unknown_type $hostName
	 * @return unknown_type
	 */
	public function setHost($hostName)
	{
		$this->setPartWithDirtyData('Host', $hostName);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getHost()
	{
		return $this->getPart('Host');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasPort()
	{
		return $this->hasPart('Port');
	}
	
	/**
	 * 
	 * @param unknown_type $port
	 * @return unknown_type
	 */
	public function setPort($port)
	{
		$this->setPartWithDirtyData('Port', $port, false);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getPort()
	{
		return $this->getPart('Port');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Url/Url_Abstract#_setParts()
	 */
	protected function setParts()
	{
		if ($this->getRegex()->hasUserInfo()) {
			$this->setPart('UserInfo', $this->getRegex()->getUserInfo());
		}
		$this->setPart('Host', $this->getRegex()->getHost());
		if ($this->getRegex()->hasPort()) {
			$this->setPart('Port', $this->getRegex()->getPort(), false);
		}	
	}
}