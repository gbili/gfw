<?php
namespace Gbili\Miner\Lexer;

use Gbili\Db\ActiveRecord\ActiveRecordInterface;
use Gbili\Out\Out;

/**
 * 
 * @author gui
 *
 */
abstract class AbstractLexer
{
    /**
     * 
     * @var unknown_type
     */
    protected $populableInstance = null;
    
	/**
	 * This allows to store data that is sahred among all instances
	 * @see storeInShared()
	 * 
	 * @var multitype
	 */
	protected $shared = array();
	
	/**
	 * Allows to wait for other entites to be populated, because
	 * some entity depends on it.
	 * The entity (depending on another for completion) can store its
	 * data here, and it will be reinjected to the dependent when the
	 * dependency becomes available
	 * array('dependantName'=>'dependencyName')
	 * 
	 * @var 
	 */
	protected $dependencies = array();
	
	/**
	 * array('dependentName' => $data, 'dependentName2' => $data2...)
	 * @var unknown_type
	 */
	protected $dependantsData = array();
	
	/**
	 * Overrides existing content
	 * @todo, need to set shared levels:problem: shared content will be lost on first populableInstance setting because of shared=array(), if exception check is removed then its a litle fucked up
	 * @param array $dumpingData key value pairs of data
	 * that must be retrieved later, $entity=>$value
	 * @return unknown_type
	 */
	protected function storeInSharedContent(array $data)
	{
		foreach ($data as $entity => $value) {
		    if (isset($this->shared[$entity]) && $this->shared[$entity] === $value) {
		        ('This entity is already in shared, this means, that you have not injectSharedContent(), so you are loosing mined data... It may be because there is no populableInstance');
		    }
			$this->shared[$entity] = $value;
		}
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	protected function hasSharedContent()
	{
		return !empty($this->shared);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getSharedContent()
	{
		return $this->shared;
	}
	
	/**
	 * Inject shared and empty it
	 * @param \Gbili\Db\ActiveRecord\ActiveRecordInterface $instance
	 * @return unknown_type
	 */
	protected function injectSharedContent()
	{
	    if ($this->hasSharedContent()) {
    	    $this->populateInstance($this->getPopulableInstance(), $this->getSharedContent());
	    }
	}
	
	/**
	 * Store the value of an entity until $dependency entitiy
	 * which the former depends upon, 
	 * 
	 * @param unknown_type $dependency
	 * @param unknown_type $dependant
	 * @param unknown_type $values
	 */
	public function setDependency($dependency, $dependant, $values)
	{
	    $this->dependencies[$dependant]   = $dependency;
	    $this->dependantsData[$dependant] = $values;
	}
	
	/**
	 * Inject the shared content when instance is set
	 * and only then. (Avoids shared content duplicate setting)
	 * 
	 * @param ActiveRecordInterface $instance
	 */
	public function setPopulableInstance($instance)
	{
	    $this->populableInstance = $instance;
	    $this->injectSharedContent();
	}
	
	/**
	 * 
	 * @throws Exception
	 * @return unknown_type
	 */
	public function getPopulableInstance()
	{
	    if (!$this->hasPopulableInstance()) {
	        throw new Exception('Populable instance is not set');
	    }
	    return $this->populableInstance;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function hasPopulableInstance()
	{
	    return null !== $this->populableInstance;
	}
	
	/**
	 * 
	 * @param array $data
	 */
	public function manageData(array $data)
	{
	    if (!$this->hasPopulableInstance()) {
	        $this->storeInSharedContent($data);
	        return;
	    }
	    $this->populateInstance($this->getPopulableInstance(), $data);
	    $this->handleDependencies($data);
	}
	
	/**
	 * 
	 * @param array $data
	 * @return boolean
	 */
	protected function handleDependencies(array $data)
	{
        $usefulDependants     = array_intersect($this->dependencies, array_keys($data));
        $usefulDependantsData = array_intersect_key($this->dependantsData, $usefulDependants);
        
	    if (!empty($usefulDependantsData)) {
    	    $this->populateInstance($usefulDependantsData);
    	    $this->unsetSatisfiedDependants($usefulDependants);
	    }
	}
	
	/**
	 * 
	 * @param array $data
	 */
	protected function unsetSatisfiedDependants(array $satisfiedDependants)
	{
	    $this->dependencies   = array_diff_key($this->dependencies, $usefulDependants);
	    $this->dependantsData = array_diff_key($this->dependantsData, $usefulDependants);
	}
	
	/**
	 * An object and an array of data will be passed so
	 * that the implemented function can determine what to do
	 * and how to handle the data
	 * 
	 * @param unknown_type $instance the instance that will be populated
	 * @param array $info array(:term=>:data)) the data that will be used to populate the instance
	 * @return unknown_type
	 */
	abstract public function populateInstance($instance, array $info);
	
	/**
	 * Return true or false depending on whether the term is supported or not
	 * i.e. does populateInstance() know how to handle the data for that term
	 * 
	 * @param unknown_type $term
	 * @return unknown_type
	 */
	public function isInDictionary($term)
	{
		$reflection = new \ReflectionClass($this);
		return (in_array($term, $reflection->getConstants()));
	}
	
	/**
	 * 
	 * @param unknown_type $v
	 * @return unknown_type
	 */
	public function isPlausibleValue($v)
	{
		return (is_string($v) && mb_strlen($v) > 0);
	}
}
