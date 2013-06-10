<?php
namespace Gbili\Video\Entity\Genre;

/**
 * Title and slug
 * 
 * @author gui
 *
 */
class Savable
extends \Gbili\ValueSlug\Savable
{
	public function __construct($title)
	{
		parent::__construct($title);
		$this->setCustomRequestorTableName('Genre');
	}
}