<?php
namespace Gbili\Miner\Blueprint\Savable;

use Gbili\Miner\Blueprint\Savable as SavableBlueprint;

class Wrapper
{
    /**
     * 
     * @var Gbili\Miner\Blueprint\Savable
     */
    private $blueprint = null;
    
    /**
     * 
     * @var array
     */
    private $actionSet = array();
    
    /**
     * 
     * @var number
     */
    private $currentActionIndex = 0;
    
    /**
     * 
     * @var number
     */
    private $actionToParent = array();
    
    /**
     * 
     * @param string $host
     */
    public function __construct($host = null)
    {
        if (null !== $host) {
            $this->getBlueprint()->setHost($host);
        }
    }
    
    /**
     * 
     * @param Gbili\Miner\Blueprint\Savable $bp
     */
    public function setBlueprint(SavableBlueprint $bp)
    {
        if (null !== $this->blueprint) {
            throw new Exception('Cannot reset blueprint');
        }
        $this->blueprint = $bp;
        return $this;
    }
    
    /**
     * 
     * @return Gbili\Miner\Blueprint\Savable
     */
    public function getBlueprint()
    {
        if (null === $this->blueprint) {
            $this->setBlueprint(new SavableBlueprint());
        }
        return $this->blueprint;
    }
    
    /**
     * 
     * @param number $index
     * @throws Exception
     * @return multitype:
     */
    public function getAction($index = null)
    {
        if (null === $index) {
            $index = $this->currentActionIndex;
        } 
        if (!isset($this->actionSet[$index])) {
            throw new Exception('The index is not set, action has not been created yet');
        }
        return $this->actionSet[$index];
    }
    
    /**
     * 
     * @param string $type action type
     * @param integer index
     * @return \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable
     */
    public function createChild($type = 'GetContents', $parentIndex = null)
    {
        $classname = "\\Gbili\\Miner\\Blueprint\\Action\\$type\\Savable"; 
        $child = new $classname();
        
        if (empty($this->actionSet)) {
            $this->actionSet[] = $child;
            $child->setBlueprint($this->getBlueprint());
            return $child;
        }
        
        if (null === $parentIndex) {
            $parentIndex = $this->currentActionIndex;
        }

        if ($parentIndex instanceof \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable) {
            $parentIndex $this->getActionIndex($parentIndex);
        }
        
        $this->currentActionIndex = count($this->actionSet);
        
        if (!isset($this->actionSet[$parentIndex])) {
            throw new Exception('Bad index, no action has this index: ' . $parentIndex);
        }
        
        $this->actionToParent[$this->currentActionIndex] = $parentIndex;
        
        $this->actionSet[$parentIndex]->addChild($child);
        $this->actionSet[] = $child;
        
        return $child;
    }
    
    /**
     * Returns a index to the last action that was created. Can
     * be used to get a specific parent's action
     * 
     * @throws Exception
     * @return \Gbili\Miner\Blueprint\Savable\number
     */
    public function getCurrentActionIndex()
    {
        if (empty($this->actionSet)) {
            throw new Exception('No actions for the moment, add one, and call this afterwards');
        }
        return $this->currentActionIndex;
    }
    
    /**
     * 
     * @param unknown_type $brotherIndex
     * @throws Exception
     * @return \Gbili\Miner\Blueprint\Action\Extract\Savable
     */
    public function createBrotherExtract($brotherIndex = null)
    {
        if (null === $brotherIndex) {
            $brotherIndex = $this->getCurrentActionIndex();
        }
        
        if (!isset($this->actionToParent[$brotherIndex])) {
            throw new Exception('Bad Index there is no parent for this index, or you cannot create a brother to the root action');
        }
        
        return $this->createChildExtract($this->actionToParent[$brotherIndex]);
    }
    
    /**
     * 
     * @return \Gbili\Miner\Blueprint\Action\GetContents\Savable
     */
    public function createChildGetContents($parent = null)
    {
        return $this->createChild('GetContents', $parent);
    }

    /**
     * @param \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable $action
     * @throws \Exception
     * @return integer
     */
    public function getActionIndex(\Gbili\Miner\Blueprint\Action\Savable\AbstractSavable $action)
    {
        $index = array_search($action, $this->actionSet);
        if (false === $index) {
            throw new \Exception('Action has not been added to the actionset');
        }
        return $index;
    }
    
    /**
     * 
     * @return \Gbili\Miner\Blueprint\Action\Extract\Savable
     */
    public function createChildExtract($parent = null)
    {
        return $this->createChild('Extract', $parent);
    }
}
