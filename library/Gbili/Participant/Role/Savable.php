<?php
namespace Gbili\Participant\Role;

use Gbili\Savable\Exception,
    Gbili\Slug\Slug;

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
	 * @param unknown_type $name
	 * @return unknown_type
	 */
	public function __construct($name)
	{
		parent::__construct();
		$this->setElement('name', (string) $name);
		$slug = new Slug($name);
		if (! $slug->isValid()) {
			throw new Exception($slug->getError());
		}
		$this->setElement('slug', $slug);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getName()
	{
		return $this->getElement('name');
	}
	
	/**
	 * 
	 * @return Slug
	 */
	public function getSlug()
	{
		return $this->getElement('slug');
	}
}