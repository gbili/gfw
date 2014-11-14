<?php
namespace Gbili\ValueSlug;

/**
 * Name and slug
 * 
 * Some entites just need to be identified
 * by their value, the slug, and an id
 * 
 * Just extend this if it is the case
 * 
 * @author gui
 *
 */
use Gbili\Savable\Exception;
use Gbili\Slug\Slug;

class Savable
extends \Gbili\Savable\Savable
{
	/**
	 * 
	 * @param unknown_type $value
	 * @return void
	 */
	public function __construct($value)
	{
		parent::__construct();
		//this will tell the parent to use this specified requestor
		//for all subclasses, unless they specify otherwise
		$this->setRequestorClassname(__CLASS__);
		//tell parent to tell requestor the table name
		$this->setPassTableNameToRequestor();
		
		$this->setElement('value', (string) $value);
		$slug = new Slug($value);
		if (! $slug->isValid()) {
			throw new Exception($slug->getError());
		}
		$this->setElement('slug', $slug);
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getValue()
	{
		return $this->getElement('value');
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