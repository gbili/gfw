<?php
namespace Gbili\Stdlib;

/**
 * Resolve the script dependencies 
 */
class SimpleDependencyManager
{

    protected $ordered = array();

    protected $identifiers = array();

    protected $dependants = array();
    protected $dependedOn = array();

    protected $keyDependsOnValue = array();


    public function hasIdentifier($identifier)
    {
        return in_array($identifier, $this->identifiers);
    }

    /**
     * @return boolean added
     */
    public function addIdentifier($identifier)
    {
        if (!$this->hasIdentifier($identifier)) {
            $this->identifiers[] = $identifier;
            return true;
        }
        return false;
    }

    public function addDependant($dependant)
    {
        if ($this->addIdentifier($dependant) || !$this->hasDependant($dependant)) {
            $this->dependants[] = $dependant;
            $this->keyDependsOnValues[$dependant] = array();
            return true;
        }
        return false;
    }

    public function addDependedOn($dependedOn)
    {
        if ($this->addIdentifier($dependedOn) || !$this->hasDependedOn($dependedOn)) {
            $this->dependedOn[] = $dependedOn;
            return true;
        }
        return false;
    }

    public function getNotDepending()
