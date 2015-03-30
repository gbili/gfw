<?php
namespace Gbili\Savable;

use Gbili\Out\Out,
    Gbili\Db\Registry as DbRegistry,
    Gbili\Db\ActiveRecord\ActiveRecordInterface;
/**
 * All classes wanting to get an id must extend this 
 * 
 * @author gui
 *
 */
class Savable
extends AbstractSavable
implements ActiveRecordInterface
{
	/**
	 * There are two save loops for the elements
	 * set in the instance :
	 *  - PRE_SAVE_LOOP :
	 * 		saves its elements before 
	 * 		saving this instance
	 *  - POST_SAVE_LOOP:
	 *  	save its elements after
	 *  	saving this instance
	 * This is useful when you have dependencies
	 * between elements in Db.
	 * 
	 * @var unknown_type
	 */
	const PRE_SAVE_LOOP = 0;
	const POST_SAVE_LOOP = 1;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $id;
	
	/**
	 * Allow subclasses to change the requestor class name
	 * It will be passed to DbRegistry
	 * 
	 * @var unknown_type
	 */
	private $requestorClassname = null;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $keysOfElementsToSave = array();
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $keysOfElementsInstanceOfSelf = array();
	
	/**
	 * This avoid the infinite loop
	 * when there is a two way reference
	 * 
	 * @var unknown_type
	 */
	private $hasSaveAllreadyBeenCalled = false;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $passTableNameToRequestor = false;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $customRequestorTableName = null;

	/**
	 * 
	 * @var unknown_type
	 */
	private $differentRequestorPrefixedAdapter = null;
	
	/**
	 * Allows to check whether the passed key->value is unique
	 * E.g. id->number could be unique among instances
	 * of the same class. So use setUniqueElement() 
	 * Using late static binding to avoid
	 * conflicts among different subclasses
	 * 
	 * @var multitype
	 */
	static protected $keysToUniqueValues = array();
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function __construct()
	{
		parent::__construct();
		//these will hold the elements that will be saved
		$this->keysOfElementsToSave[self::PRE_SAVE_LOOP] = array();
		$this->keysOfElementsToSave[self::POST_SAVE_LOOP] = array();
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function isPassTableNameToRequestor()
	{
		return $this->passTableNameToRequestor;
	}
	
	/**
	 * This allows the sub class to tell
	 * this class to set the requestors
	 * table name from a custom table name
	 * or if not available by guessing it
	 * 
	 * @return unknown_type
	 */
	public function setPassTableNameToRequestor()
	{
		$this->passTableNameToRequestor = true;
	}
	
	/**
	 * When this custom table name is set
	 * then this class will not try to guess
	 * it from the sub class' name
	 * 
	 * @param unknown_type $tableName
	 * @return unknown_type
	 */
	public function setCustomRequestorTableName($tableName)
	{
		if (false === $this->isPassTableNameToRequestor()) {
			$this->setPassTableNameToRequestor();
		}
		$this->customRequestorTableName = (string) $tableName;
	}
	
	/**
	 * This will try to create a table name
	 * that is the same as the class name,
	 * but without the Savable part
	 * 
	 * @return unknown_type
	 */
	protected function getTableNameGuess()
	{
		/*
		 * This was used before psr-0
		$tName = get_class($this);
		$c = strlen('_Savable');
		if ('_Savable' === substr($tName, -$c)) {
			$tName = substr($tName, 0, -$c);
		}*/
	    //this gets the name of the Sub\Class\Savable
	    $classNamePts = explode('\\', get_class($this));
	    if ('Savable' === end($classNamePts)) {
	        array_pop($classNamePts);//remove Savable
	    }
		return implode('_', $classNamePts);//return Sub_Class
	}
	
	/**
	 * return the id and save if
	 * necessary, to retrieve it
	 * 
	 * @return unknown_type
	 */
	final public function getId()
	{
		if (null === $this->id) {
			$this->save();
		}
		return $this->id;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	final public function hasId()
	{
		return (null !== $this->id);
	}
	
	/**
	 * This can only be called if the Db
	 * input lock is opened
	 * 
	 * @param unknown_type $id
	 * @return unknown_type
	 */
	final public function setId($id)
	{
		$this->id = $id;
		return $this;
	}
	
	/**
	 * Allows to tell the requestor
	 * that it has to talk to the Db with
	 * a different prefixed adapter than the one
	 * specified by the subclass requestor's class
	 * name (which is used as prefix)
	 * 
	 * @param unknown_type $prefix
	 * @return unknown_type
	 */
	public function setDifferentRequestorPrefixedAdapter($prefix)
	{
		$this->differentRequestorPrefixedAdapter = $prefix;;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getDifferentRequestorPrefixedAdapter()
	{
		if (null === $this->differentRequestorPrefixedAdapter) {
			throw new Exception('the different requestor prefixed adapter is not set');
		}
		return $this->differentRequestorPrefixedAdapter;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function useDifferentRequestorPrefixedAdapter()
	{
		return null !== $this->differentRequestorPrefixedAdapter;
	}
	
	/**
	 * Allow subclass to change the requestor class name
	 * 
	 * @param unknown_type $className
	 * @return unknown_type
	 */
	public function setRequestorClassname($className)
	{
		$this->requestorClassname = (string) $className;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getRequestorClassname()
	{
		return $this->requestorClassname;
	}
	
	/**
	 * The behaviour of setElement is change to output
	 * all elements when callin to array
	 * 
	 * (non-PHPdoc)
	 * @see Savable/Savable_Abstract#_setElement($key, $value, $keyInToArrayReturnArray)
	 */
	protected function setElement($key, $value, $saveLoop = self::PRE_SAVE_LOOP)
	{
		parent::setElement($key, $value, true);//put all keys in return array
		$this->manageSaveLoop($key, $value, $saveLoop);
	}
	
	/**
	 * Sometimes instances of a class should have unique
	 * properties
	 * 
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @param unknown_type $saveLoop
	 * @throws Exception
	 */
	protected function setUniqueElement($key, $value, $saveLoop = self::PRE_SAVE_LOOP)
	{
	    $scopeClass = get_called_class();
	    if (!isset(self::$keysToUniqueValues[$scopeClass])) {
	        self::$keysToUniqueValues[$scopeClass] = array();
	    }
	    
	    if (!isset(self::$keysToUniqueValues[$scopeClass][$key])) {
	        self::$keysToUniqueValues[$scopeClass][$key] = array();
	    }
	    
	    if (in_array($value, self::$keysToUniqueValues[$scopeClass][$key])) {
	        throw new Exception("UniqueConflict in $scopeClass, the key : '$key' with value : '$value' was already used in class (maybe other instance), use different value");
	    }
	    
	    self::$keysToUniqueValues[$scopeClass][$key][] = $value;
	    
	    $this->setElement($key, $value, $saveLoop);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Savable/Savable_Abstract#_useKeyAsArrayAndPushValue($key, $value, $keyInToArrayReturnArray)
	 */
	protected function useKeyAsArrayAndPushValue($key, $value, $saveLoop = self::PRE_SAVE_LOOP)
	{
		parent::useKeyAsArrayAndPushValue($key, $value, true);
		$this->manageSaveLoop($key, $value, $saveLoop);
	}
	
	/**
	 * 
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @param unknown_type $saveLoop
	 * @return unknown_type
	 */
	private function manageSaveLoop($key, $value, $saveLoop)
	{
		//if it is instance of self or an array of instances of self
		// add its key to pre|post save array so it will be saved
		if (($value instanceof self 
				|| (is_array($value) 
					&& array_keys($value) === range(0, count($value) - 1)
					&& current($value) instanceof self)) //only save arrays that have elements of savable
			&& !in_array($key, $this->keysOfElementsToSave[$saveLoop])) {
			//this will avoid the instance passed in $value to be saved before this one
			//this way, the id of this instance will be available to $value instance
			$this->keysOfElementsToSave[$saveLoop][] = $key;
		}
	}
	
	/**
	 * This will store the savable in the Db
	 * and it will populate the id
	 * 
	 * @return unknown_type
	 */
	public function save()
	{
		//Out::l3('entering savable::save() of '. get_class($this) . "\n");
		if (true === $this->hasSaveAllreadyBeenCalled) {
			//Out::l3('exiting already called savable::save() of '. get_class($this) . "\n");
			return;
		}
		//this will avoid the infinite loop in case there
		//are two instances referencing one an other and 
		//they are both in their respective PRE_SAVE_LOOP
		//when this case arises it is the result of bad
		//programming so it will throw an exception in
		//_saveIfNeeded();
		$this->hasSaveAllreadyBeenCalled = true;
		
		//Out::l3('>----opening------>PRE SAVE LOOP >----------> of '. get_class($this) . "\n");
		/*
		 * ----------------- PRE SAVE > save all instances that this one depends on to save itself
		 */
		if (!empty($this->keysOfElementsToSave[self::PRE_SAVE_LOOP])) {
			$this->saveLoop(self::PRE_SAVE_LOOP);
		}
		/*
		 * ----------------- SAVE > save the current instance
		 */
		//Out::l3('<---closing--<PRE SAVE LOOP <---------------< of '. get_class($this) . "\n");
		//allow subclass to change the requestor class name
		$registryParam = (null !== $this->requestorClassname)? $this->requestorClassname : $this;
		
		//once the tree has been saved, this instance 
		//now Db can save and set the id, because it has all it needs
		//Out::l3('calling requestor::save() of '. get_class($this) . "\n");
		$reqInst = DbRegistry::getInstance($registryParam);
		
		//allow to change the requestor Db adapter
		if ($this->useDifferentRequestorPrefixedAdapter()) {
			$reqInst->setDifferentPrefixedAdapter($this->getDifferentRequestorPrefixedAdapter());
		}
		
		//allow to tell the requestor what table name to work on
		if ($this->isPassTableNameToRequestor()) {
			$reqInst->setTableName((string) (null === $this->customRequestorTableName)? $this->getTableNameGuess() : $this->customRequestorTableName);
		}
		$reqInst->save($this);

		//Db must set the id of this class once saved
		if (null === $this->id) {
			throw new Exception('Your Db talker must set the Savable instance\'s id.');
		}
		//Out::l3('>----opening------>POST SAVE LOOP >----------> of '. get_class($this) . "\n");
		/*
		 * ----------------- POST SAVE > save instances that on this ones id to save themseleves
		 */
		if (!empty($this->keysOfElementsToSave[self::POST_SAVE_LOOP])) {
			$this->saveLoop(self::POST_SAVE_LOOP);
		}
		//Out::l3('<---closing--<POST SAVE LOOP <---------------< of '. get_class($this) . "\n");
        self::$keysToUniqueValues = array();
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasSaveAllreadyBeenCalled()
	{
		return $this->hasSaveAllreadyBeenCalled;
	}
	
	/**
	 * 
	 * @param unknown_type $type
	 * @return unknown_type
	 */
	private function saveLoop($type)
	{
		foreach ($this->keysOfElementsToSave[$type] as $key) {
			$e = $this->getElement($key);
			if (!is_array($e)) {
				$this->saveIfNeeded($key, $e, $type);
			} else {
				foreach ($e as $elm) {
					$this->saveIfNeeded($key, $elm, $type);
				}
			}
		}
	}
	
	/**
	 * Determines whether to save or not it avoids duplicate savings
	 * 
	 * @param Savable $e
	 * @return unknown_type
	 */
	private function saveIfNeeded($k, Savable $e, $callingLoopType = self::PRE_SAVE_LOOP)
	{	
		if ($e->hasId()) {//only if it has not already been successfully saved
			return;
		}
		
		if ($callingLoopType === self::PRE_SAVE_LOOP
		 && !in_array($k, $this->keysOfElementsToSave[self::POST_SAVE_LOOP])) {
			if ($e->hasSaveAllreadyBeenCalled()) {
				throw new Exception('You have a two way reference and both elements are in one\'s other PRE_SAVE_LOOP, which results in an infinite loop (hadn\'t this exception been thrown), you can solve this by setting one of those references in the POST_SAVE_LOOP, by calling setElement(key, value, Savable::POST_SAVE_LOOP) : ' . $k . ', ' . print_r($e,true));
			}
			//if the element does not require $this to be saved before itself
			//Out::l3("PRE LOOP : saving $k from " . get_class($this) . "\n");
			$e->save();
		} else if ($callingLoopType === self::POST_SAVE_LOOP) {
			//Out::l3("POST LOOP : saving $k from " . get_class($this) . "\n");
			$e->save();
		}
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function delete()
	{
		//allow subclass to change the requestor class name
		$registryParam = (null !== $this->requestorClassname)? $this->requestorClassname : $this;
		DbRegistry::getInstance($registryParam)->delete($this);
	}
}
