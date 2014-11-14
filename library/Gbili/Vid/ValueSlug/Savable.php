<?php
namespace Gbili\Vid\ValueSlug;

/**
 * Extend the parent so there will be no need to specify
 * the different requestor prefixed adapter for every
 * subclass
 * 
 * @author gui
 *
 */
class Savable
extends \Gbili\ValueSlug\Savable
{
	public function __construct($value)
	{
		parent::__construct($value);
		$this->setDifferentRequestorPrefixedAdapter('\\Gbili\\Vid');
	}
}