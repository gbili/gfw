<?php
namespace Gbili\Stdlib;

class CircularCollection extends Collection
{
    /**
     * We want to be able to return the first
     * element in getNext, because when no element
     * has been returned already, it is considered 
     * to be the logical next element.
     * 
     * @var number
     */
    protected $firstElementHasBeenNexted = false;
    
    /**
     * 
     * @var number
     */
    protected $lap = 0;
    
    /**
     * We want to be able to tell if the call to
     * getNext() made us change lap or not.
     * @var unknown_type
     */
    protected $lastCallToGetNextChangedLap = false;
    
    /**
     * The first lap is 0
     * @return number
     */
    public function getLap()
    {
        return $this->lap;
    }
    
    /**
     * The first element in the collection
     * is considered the next element when
     * no elements have been returned yet.
     * So the first time getNext is called,
     * it will return the first element and
     * not the second as expected...
     * 
     * Advance pointer and return next,
     * that it returns only the value
     * and rewinds in case reached end
     * @return mixed
     */
    public function getNext()
    {
        if (!$this->firstElementHasBeenNexted) {
            $this->firstElementHasBeenNexted = true;
            return $this->getCurrent();
        }
        
        $this->next();
        
        if (!$this->valid()) {
            $this->nextLap();
        } else {
            $this->lastCallToGetNextChangedLap = false;
        }
        
        return $this->getCurrent();
    }
    
    /**
     * Did getNext() return the last element in collection
     * @throws Exception
     * @return boolean
     */
    public function lastCallToGetNextChangedLap()
    {
        if ($this->isEmpty()) {
            throw new Exception('Empty collection, makes no sense to check if end of lap');
        }
        return $this->lastCallToGetNextChangedLap;
    }
    
    /**
     * 
     */
    public function firstElementHasBeenNexted()
    {
        return $this->firstElementHasBeenNexted;
    }

    /**
     * 
     * @throws Exception
     */
    public function nextLap()
    {
        if ($this->isEmpty()) {
            throw new Exception('Cannot advance lap on an empty collection');
        }
        $lapBeforeRewind                 = $this->lap;
        $this->lastCallToGetNextChangedLap     = true;
        $this->rewind();
        return $this->lap = ++$lapBeforeRewind;
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasChangedLap()
    {
        return $this->lastCallToGetNextChangedLap() && !$this->firstElementHasBeenNexted();
    }
    
    /**
     * 
     */
    public function rewind()
    {
        $this->lap                       = 0;
        $this->firstElementHasBeenNexted = false;
        return parent::rewind();
    }
}
