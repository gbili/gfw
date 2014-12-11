<?php
namespace Gbili\Miner\Blueprint\Action\Savable;

use Gbili\Savable\Savable;
use Gbili\Miner\Blueprint;
use Gbili\Miner\Blueprint\Savable as BlueprintSavable;
use Gbili\Miner\Blueprint\Action\Savable\Db\Req;
use Gbili\Miner\Blueprint\Action\Extract\Savable as ExtractSavable;
use Gbili\Miner\Blueprint\Action\GetContents\Savable as GetContentsSavable;

/**
 * This class is not meant for any great work, just to ensure
 * that the action gets all its data. And that it gets saved properly
 * 
 * extending classes must set the 'type' automatically on construction
 * 
 * 
 * @author gui
 *
 */
abstract class AbstractSavable extends Savable
{

	private static $order = array();
	
	/**
     * When there is no capturing group in parent
     * then the matched string will be in result
     * with index 0 
     *
	 * @var integer 
	 */
	const DEFAULT_INPUT_PARENT_REGEX_GROUP_NUMBER = 0;
	
    /**
     * Used when parent is of type get contents
     *
     * @var integer
     */
	const DEFAULT_NO_INPUT_PARENT_REGEX_GROUP_NUMBER = 0;

	/**
	 * Change requestor class name
	 * that will be used by Savable_Savable
	 * when calling method save()
	 * @return unknown_type
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setRequestorClassname(__NAMESPACE__);
	}
	
	/**
	 * 
	 * @param string $title
	 * @return \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable
	 */
	public function setTitle($title)
	{
		$this->setUniqueElement('title', (string) $title);
		return $this;
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
	 * @return unknown_type
	 */
	public function getTitle()
	{
		return $this->getElement('title');
	}
	
	/**
	 * When calling this method, child should not have been added
	 * to parent
	 * 
	 * @param unknown_type $parent
	 * @param unknown_type $child
	 * @return unknown_type
	 */
	private function manageOrder($parent, $child)
	{
		//see if parent has a rank
		if (false === ($parentRnk = self::getOrderRank($parent))) {
			if (!empty(self::$order)) {
				throw new Exception('The parent has no rank but some ranks have already been set, you must start adding childs from root cannot create a branch and then add it to trunk' . print_r(array_keys(self::$order), true));
			}
			//this means the parent is root so it is setting itself
			self::$order[] = $parent;
		}
		self::$order[] = $child;
	}
	
	/**
	 * 
	 * @param unknown_type $element
	 * @return unknown_type
	 */
	public static function getOrderRank($element)
	{
		return array_search($element, self::$order, true);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getRank()
	{
		if (false === $rnk = self::getOrderRank($this)) {
			throw new Exception('the rank has not been set for this instance, it means that it was not chained to any parent (or child, when root)');
		}
		return $rnk;
	}
	
	/**
	 * This allows the action to be skipped and all its
	 * children when the parent action being extract did
	 * not generate any input for it
	 * 
	 * @return \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable
	 */
	public function setAsOptional()
	{
		$this->setElement('isOpt', true);
		
		return $this;
	}

	/**
	 * 
	 * @return bool
	 */
	public function getIsOptional()
	{
		if (!$this->isSetKey('isOpt')) {
			$this->setElement('isOpt', false);
		}
		return $this->getElement('isOpt');
	}
	
	/**
	 * You can explicitely instantiate the right class or use this function
	 * to get the class of the type specified in param
	 * 
	 * @param integer $type
	 * @return \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable
	 */
	public static function getInstanceOfType($type)
	{
		switch ($type) {
			case Blueprint::ACTION_TYPE_EXTRACT;
				$instance = new ExtractSavable();
				break;
			case Blueprint::ACTION_TYPE_GETCONTENTS;
				$instance = new GetContentsSavable();
				break;
			default;
				throw new Exception('The type you passed is not supported given : ' . print_r($type, true));
				break;
		}
		return $instance;
	}
	
	/**
	 * @TODO the new instance generatig point has a flaw when it is attached to an Extract action with matchAll
	 * we have to hook someplace the new instance generation so that there is an instance for every match in matchall
	 * 1. find at which point matchAll can give a hint on the numer of results.
	 * 2. once we have the number, add some sort of communication between the Extract action, and the Persistance::manageNIGP()
	 * 3. make sure that all those instances are saved gracefully.
	 * 
	 * @return \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable
	 */
	public function setAsNewInstanceGeneratingPoint()
	{
		if (!$this->isSetKey('blueprint')) {
			throw new Exception('The blueprint must be set with setBlueprint() before setting the action as newInstanceGeneratingPoint');
		}
		$this->getBlueprint()->setNewInstanceGeneratingPointAction($this);
		$this->setElement('isNewInstanceGeneratingPoint', 1);
		
		return $this;
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function isNewInstanceGeneratingPoint()
	{
		return $this->isSetKey('isNewInstanceGeneratingPoint');
	}
	
	/**
	 * Also sets the element blueprintId
	 * 
	 * @param BlueprintSavable $b
	 * @return \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable
	 */
	public function setBlueprint(BlueprintSavable $b)
	{
		$this->setElement('blueprint', $b);
		
		return $this;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasBlueprint()
	{
		return $this->isSetKey('blueprint');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getBlueprint()
	{
		return $this->getElement('blueprint');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function isRoot()
	{
		return ($this->getType() === Blueprint::ACTION_TYPE_GETCONTENTS && !$this->isSetKey('parentAction'));
	}
	
	/**
	 * 
	 * @param $parentId
	 * @return \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable
	 */
	public function setParent(AbstractSavable $action)
	{
		$this->setElement('parentAction', $action);
		
		return $this;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasParent()
	{
		return $this->isSetKey('parentAction');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getParent()
	{
		return $this->getElement('parentAction');
	}
	
	/**
	 * 
	 * @return \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable
	 */
	public function addChild(AbstractSavable $action)
	{
		$action->setBlueprint($this->getBlueprint());
		$action->setParent($this);
		//this will set the rank of the parent and child
		$this->manageOrder($this, $action);
		$this->useKeyAsArrayAndPushValue('childAction', $action, Savable::POST_SAVE_LOOP);
		
		return $this;
	}
	
	/**
	 * 
	 * @param Blueprint_Action_Savable_Abstract $action
	 * @param unknown_type $inputGroup
	 * @return \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable
	 */
	public function injectResultTo(AbstractSavable $action, $inputGroup = null)
	{
		if (null !== $inputGroup) {
			if (!($this instanceof ExtractSavable)) {
				throw new Exception('You cannot set the input group if the injecting action is not of type extract');
			}
			$action->setInjectInputGroup($inputGroup);
		}
		$this->setElement('injectedAction', $action);
		
		return $this;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function injectsAction()
	{
		return $this->isSetKey('injectedAction');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getInjectedAction()
	{
		return $this->getElement('injectedAction');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasInjectInputGroup()
	{
		return $this->isSetKey('injectInputGroup');
	}
	
	/**
	 * 
	 * @param unknown_type $group
	 * @return \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable
	 */
	public function setInjectInputGroup($group)
	{
		$this->setElement('injectInputGroup', (integer) $group);
		
		return $this;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getInjectInputGroup()
	{
		return $this->getElement('injectInputGroup');
	}

	/**
	 * 
	 * @return array of Blueprint_Action_Savable_Abstract
	 */
	public function getChildren()
	{
		return $this->getElement('childAction');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasChildren()
	{
		return $this->isSetKey('childAction');
	}

	/**
	 * 
	 * @param $inputParentRegexGroup
	 * @return \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable
	 */
	public function setInputParentRegexGroup($inputParentRegexGroup)
	{
		$this->setElement('inputParentRegexGroup', $inputParentRegexGroup);
		return $this;
	}
	
	/**
	 * Sets the input group to default when the parent is of type extract
	 * otherwise it sets the input group to no group specified in requestor
	 * 
	 * 
	 * @return unknown_type
	 */
	public function getInputParentRegexGroup()
	{
		if (!$this->isSetKey('inputParentRegexGroup')) {
			if ($this->hasParent() && $this->getParent()->getType() === Blueprint::ACTION_TYPE_EXTRACT) {
				$this->setInputParentRegexGroup(self::DEFAULT_INPUT_PARENT_REGEX_GROUP_NUMBER);
			} else {
				$this->setInputParentRegexGroup(self::DEFAULT_NO_INPUT_PARENT_REGEX_GROUP_NUMBER);
			}
		}
		return $this->getElement('inputParentRegexGroup');
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @return \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable
	 */
	public function setData($data)
	{
		$this->setElement('data', (string) $data);
		
		return $this;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getData()
	{
		return $this->getElement('data');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasData()
	{
		return $this->isSetKey('data');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getType()
	{
		return $this->getElement('type');
	}
}
