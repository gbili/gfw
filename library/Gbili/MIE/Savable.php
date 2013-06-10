<?php
namespace Gbili\MIE;

/**
 * MIE stands for Movie Industry Entity which are 
 * all entities (person|studio) related in the process
 * of making a movie.
 * 
 * They are subdivided into categories into
 * Participant :
 * 	-actor
 * 	-producer
 * 	-director
 * 
 * @author gui
 *
 */
class Savable
extends \Gbili\Savable\Savable
{	
	/**
	 * 
	 * @param $name
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
	 * @return unknown_type
	 */
	public function getSlug()
	{
		return $this->getElement('slug');
	}
	
	//public function setCountry()
}