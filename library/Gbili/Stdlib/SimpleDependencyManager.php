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
    {
        return array_diff($this->identifiers, $this->dependants);
    }

    /**
     *
     * @param $dependant the script identifier that depends on another script identifier
     * @param $dependedOn the script identifier on which other scripts depend
     * @retur self
     */
    public function addDependency($dependant, $dependedOn)
    {
        $this->addDependant($dependant);
        $this->addDependedOn($dependedOn);

        if (!$this->hasDependency($dependant, $dependedOn)) {
            $this->keyDependsOnValues[$dependant][] = $dependedOn;
        }
        return $this;
    }

    protected function hasDependant($dependant)
    {
        return in_array($dependant, $this->dependants);
    }

    protected function hasDependedOn($dependedOn)
    {
        return in_array($dependedOn, $this->dependedOn);
    }

    protected function hasDependency($dependant, $dependedOn)
    {
        return $this->hasDependant($dependant) && in_array($dependedOn, $this->keyDependsOnValues[$dependant]);
    }

    protected function orderDependencies()
    {
        //Printed first in any order
        $notDepending = $this->getNotDepending();
        $this->ordered = $notDepending;

        $dependingAndDependedOn = array_diff($this->dependedOn, $notDepending);

        $this->addToOrderedThoseWhoseDependencyIsAlreadyInOrdered($dependingAndDependedOn);
 
        //Printed last in any order
        $onlyDepending = array_diff($this->dependants, $dependingAndDependedOn);
        foreach ($onlyDepending as $identifier) {
            $this->ordered[] = $identifier;
        }
    }

    /**
     * @return array of depency identifiers sorted: 
     *  first is least dependant || (least dependant && most depended), 
     *  last is least depended || (least depended and most dependant)
     *  or something those lines 
     *
    public function testOrderIsOneOfTheExpected()
    {
        $this->addDependency('jQuery', 'js');
        $this->addDependency('Masonry', 'jQuery');
        $this->addDependency('franc', 'Masonry');
        $this->addDependency('FormPlugin', 'jQuery');
        $this->addDependency('myForm', 'FormPlugin');

        $ok[] = array('js', 'jQuery', 'Masonry', 'FormPlugin', 'myForm', 'franc');
        $ok[] = array('js', 'jQuery', 'Masonry', 'FormPlugin', 'franc', 'myForm');
        $ok[] = array('js', 'jQuery', 'FormPlugin', 'Masonry', 'myForm', 'franc');
        $ok[] = array('js', 'jQuery', 'FormPlugin', 'Masonry', 'franc', 'myForm');

        $ordered = $this->getOrdered();
        foreach ($ok as $validResult) {
            $diff = array_diff($validResult, $ordered);
            if (empty($diff)) {
                return true;
            }
        }
        return false;
    }
     */
    public function getOrdered()
    {
        $this->orderDependencies();
        return $this->ordered;
    }


    protected function addToOrderedThoseWhoseDependencyIsAlreadyInOrdered($dependingAndDependedOn)
    {
        foreach ($dependingAndDependedOn as $identifier) {
            if (in_array($identifier, $this->ordered)) {
                continue;
            }
            $dependenciesNotInOrdered = array_diff($this->keyDependsOnValues[$identifier], $this->ordered);
            if (empty($dependenciesNotInOrdered)) {
                $this->ordered[] = $identifier;
                continue;
            }
            $this->addToOrderedThoseWhoseDependencyIsAlreadyInOrdered($dependenciesNotInOrdered);
        }
    }
}
