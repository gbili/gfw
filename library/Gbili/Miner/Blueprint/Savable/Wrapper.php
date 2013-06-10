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
    private $currentActionPointer = 0;
    
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
            $index = $this->currentActionPointer;
        } 
        if (!isset($this->actionSet[$index])) {
            throw new Exception('The index is not set, action has not been created yet');
        }
        return $this->actionSet[$index];
    }
    
    /**
     * 
     * @param string $type action type
     * @param integer pointer
     * @return \Gbili\Miner\Blueprint\Action\Savable\AbstractSavable
     */
    public function createChild($type = 'GetContents', $parentPointer = null)
    {
        $classname = "\\Gbili\\Miner\\Blueprint\\Action\\$type\\Savable"; 
        $child = new $classname();
        
        if (empty($this->actionSet)) {
            $this->actionSet[] = $child;
            $child->setBlueprint($this->getBlueprint());
            return $child;
        }
        
        if (null === $parentPointer) {
            $parentPointer = $this->currentActionPointer;
        }
        
        $this->currentActionPointer = count($this->actionSet);
        
        if (!isset($this->actionSet[$parentPointer])) {
            throw new Exception('Bad pointer, no action has this pointer: ' . $parentPointer);
        }
        
        $this->actionToParent[$this->currentActionPointer] = $parentPointer;
        
        $this->actionSet[$parentPointer]->addChild($child);
        $this->actionSet[] = $child;
        
        return $child;
    }
    
    /**
     * Returns a pointer to the last action that was created. Can
     * be used to get a specific parent's action
     * 
     * @throws Exception
     * @return \Gbili\Miner\Blueprint\Savable\number
     */
    public function getCurrentActionPointer()
    {
        if (empty($this->actionSet)) {
            throw new Exception('No actions for the moment, add one, and call this afterwards');
        }
        return $this->currentActionPointer;
    }
    
    /**
     * 
     * @param unknown_type $brotherPointer
     * @throws Exception
     * @return \Gbili\Miner\Blueprint\Action\Extract\Savable
     */
    public function createBrotherExtract($brotherPointer = null)
    {
        if (null === $brotherPointer) {
            $brotherPointer = $this->getCurrentActionPointer();
        }
        
        if (!isset($this->actionToParent[$brotherPointer])) {
            throw new Exception('Bad Pointer there is no parent for this pointer, or you cannot create a brother to the root action');
        }
        
        return $this->createChildExtract($this->actionToParent[$brotherPointer]);
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
     * 
     * @return \Gbili\Miner\Blueprint\Action\Extract\Savable
     */
    public function createChildExtract($parent = null)
    {
        return $this->createChild('Extract', $parent);
    }
}