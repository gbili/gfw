<?php
namespace Gbili;

/**
 * 
 * @author gui
 *
 */
class Kinship
{
	/**
	 * 
	 * @var unknown_type
	 */
	private $chainedObject;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $parent;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $childrenArray = array();
	
	/**
	 * 
	 * @param unknown_type $chainedObject
	 * @return unknown_type
	 */
	public function __construct($chainedObject)
	{
		$this->chainedObject = $chainedObject;
	}
	
	/**
	 * 
	 * @param Kinship $child
	 * @return unknown_type
	 */
	public function addChild(Kinship $child, $andSetItsParent = true)
	{
		$this->childrenArray[] = $child;
		if (true === $andSetItsParent) {
			$child->setParent($this, false);
		}
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getNextChild()
	{
		if ($this->hasChildren()) {
			throw new Kinship_Exception('Child not set');
		}
		if (!($array = each($this->childrenArray))) {
			reset($this->childrenArray);
			return false;
		} else {
			return $array[1]; //return the value of the each result
		}
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getChildren()
	{
		return $this->childrenArray;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasChildren()
	{
		return empty($this->childrenArray);
	}
	
	/**
	 * 
	 * @param Kinship $parent
	 * @return unknown_type
	 */
	public function setParent(Kinship $parent, $andSetItsChild = true)
	{
		$this->parent = $parent;
		if (true === $andSetItsChild) {
			$this->parent->addChild($this, false);
		}
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getParent()
	{
		if (!$this->hasParent()) {
			throw new Kniship_Exception('Parent not set');
		}
		return $this->parent;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasParent()
	{
		return (null !== $this->parent);
	}

	/**
	 * 
	 * @param Kinship $brother
	 * @return unknown_type
	 */
	public function addBrother(Kinship $brother)
	{
		$this->getParent()->addChild($brother);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getBrothers()
	{
		return $this->getParent()->getChildren();
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getNextBrother()
	{
		return $this->getParent()->getNextChild();
	}
}