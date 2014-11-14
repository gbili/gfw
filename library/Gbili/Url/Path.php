<?php
namespace Gbili\Url;

use Gbili\Regex\Encapsulator\AbstractEncapsulator;

class Path
extends AbstractEncapsulator
{
	
	/**
	 * (non-PHPdoc)
	 * @see Gbili\Regex\Encapsulator.AbstractEncapsulator::partsToString()
	 */
	protected function partsToString()
	{
		return $this->getDirectory() . (($this->hasFileName())? $this->getFileName() : '') . (($this->hasFileExtension())? ':' . $this->getPort() : '');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasFileName()
	{
		return $this->hasPart('FileName');
	}

	/**
	 * 
	 * @param $userInfo
	 * @return unknown_type
	 */
	public function setFileName($fileName)
	{
		$this->setPartWithDirtyData('FileName', $fileName, false);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getFileName()
	{
		return $this->getPart('FileName');		
	}
	
	/**
	 * 
	 * @param unknown_type $hostName
	 * @return unknown_type
	 */
	public function setDirectory($dir)
	{
		$this->setPartWithDirtyData('Directory', $dir, false);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getDirectory()
	{
		return $this->getPart('Directory');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasFileExtension()
	{
		return $this->hasPart('FileExtension');
	}
	
	/**
	 * 
	 * @param unknown_type $port
	 * @return unknown_type
	 */
	public function setFileExtension($port)
	{
		$this->setPartWithDirtyData('FileExtension', $port, false);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getFileExtension()
	{
		return $this->getPart('FileExtension');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Url/Url_Abstract#_setParts()
	 */
	protected function setParts()
	{
		$this->setPart('Directory', $this->getRegex()->getDirectory(), false);
		if ($this->getRegex()->hasFileName()) {
			$this->setPart('FileName', $this->getRegex()->getFileName(), false);
		}
		if ($this->getRegex()->hasFileExtension()) {
			$this->setPart('FileExtension', $this->getRegex()->getFileExtension(), false);
		}
	}
}