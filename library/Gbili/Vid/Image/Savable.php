<?php
namespace Gbili\Vid\Image;

class Savable
extends \Gbili\Image\Savable
{
	public function __construct()
	{
		parent::__construct();
		//change requestor adapter
		$this->setDifferentRequestorPrefixedAdapter('\\Gbili\\Vid');
		//use parent requestor
		$this->setRequestorClassname('\\Gbili\\Image\\Savable');
		//set different table name
		$this->setCustomRequestorTableName($this->getTableNameGuess());
	}
}