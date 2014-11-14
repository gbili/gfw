<?php
namespace Gbili\Vid;

use Gbili\Vid\Tag\Savable      as TagSavable,
    Gbili\Vid\Title\Savable    as TitleSavable,
    Gbili\Vid\Image\Savable    as ImageSavable,
    Gbili\Vid\Category\Savable as CategorySavable,
    Gbili\Time\AgoToDate,
    Gbili\Source\Savable       as SourceSavable;

/**
 * Slimple Minimal Video data
 * @author gui
 *
 */
class Savable
extends \Gbili\Savable\Savable
{
	/**
	 * 
	 * @return unknown_type
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setPassTableNameToRequestor();
	}

	/**
	 * 
	 * @param unknown_type $title
	 * @return unknown_type
	 */
	public function setTitle(TitleSavable $title)
	{
		$this->setElement('title', $title);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasTitle()
	{
		return $this->isSetKey('title');
	}
	
	/**
	 * 
	 * @param unknown_type $title
	 * @return unknown_type
	 */
	public function getTitle()
	{
		return $this->getElement('title');
	}
	
	/**
	 * 
	 * @param unknown_type $category
	 * @return unknown_type
	 */
	public function setCategory(CategorySavable $category)
	{
		$this->setElement('category', $category);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasCategory()
	{
		return $this->isSetKey('category');
	}
	
	/**
	 * 
	 * @param unknown_type $category
	 * @return unknown_type
	 */
	public function getCategory()
	{
		return $this->getElement('category');
	}
	
	/**
	 * 
	 * @param unknown_type $tag
	 * @return unknown_type
	 */
	public function addTag(TagSavable $tag)
	{
		$this->useKeyAsArrayAndPushValue('tag', $tag);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasTags()
	{
		return $this->isSetKey('tag');
	}
	
	/**
	 * 
	 * @param unknown_type $tag
	 * @return unknown_type
	 */
	public function getTags()
	{
		return $this->getElement('tag');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getDate()
	{
		return $this->getElement('date');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasDate()
	{
		return ($this->isSetKey('date'));
	}

	/**
	 * 
	 * @param Country $country
	 * @return unknown_type
	 */
	public function setDate(AgoToDate $date)
	{
		$this->setElement('date', $date);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function setImage(ImageSavable $img)
	{
		$this->setElement('image', $img);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasImage()
	{
		return $this->isSetKey('image');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getImage()
	{
		return $this->getElement('image');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function setSource(SourceSavable $src)
	{
		$this->setElement('src', $src);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getSource()
	{
		return $this->getElement('src');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasSource()
	{
		return $this->isSetKey('src');
	}

	/**
	 * 
	 * @param unknown_type $timeLength
	 * @return unknown_type
	 */
	public function setTimeLength($timeLength)
	{
		$this->setElement('timeLength', $timeLength);
	}

	/**
	 * 
	 * @return unknown_type
	 */
	public function getTimeLength()
	{
		return $this->getElement('timeLength');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasTimeLength()
	{
		return $this->isSetKey('timeLength');
	}
}