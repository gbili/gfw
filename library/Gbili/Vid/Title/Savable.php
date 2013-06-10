<?php
namespace Gbili\Vid\Title;

/**
 * Title and slug
 * 
 * @author gui
 *
 */
class Savable
extends \Gbili\Vid\ValueSlug\Savable
{
	public function __construct($title)
	{
		parent::__construct($title);
		$this->setPassTableNameToRequestor();
	}
}