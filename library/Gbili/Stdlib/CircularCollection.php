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
     * The last two calls to getNext must have returned
     * the collection last element and then the first
     * element for this to be true
     */
    protected $lastCallToGetNextChangedLap = false;
    
    /**
     * How many times the circular collection reached
     * end.
     * So when going through elements of the colleciton
     * for the first time, it has never reached end,
     * that is why the lap number will be 0
     * @var number
     */
    protected $lap = 0;
    
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
     * not the second element as expected...
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

        $this->lastCallToGetNextChangedLap = false;

        $this->next();
        if (!$this->valid()) {
            $this->nextLap();
            //on this lap change, get next returns first element
            $this->firstElementHasBeenNexted = true;
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
     * Set the collection as in construction, 
     * but increase the lap
     * 
     * @throws Exception
     */
    public function nextLap()
    {
        if ($this->isEmpty()) {
            throw new Exception('Cannot advance lap on an empty collection');
        }
        $lapBeforeRewind = $this->lap;
        $this->rewind();
        $this->lastCallToGetNextChangedLap = true;
        return $this->lap = ++$lapBeforeRewind;
    }
    
    /**
     * @alias lastCallToGetNextChangedLap
     * @return boolean
     */
    public function hasChangedLap()
    {
        return $this->lastCallToGetNextChangedLap();
    }
    
    /**
     * 
     */
    public function rewind()
    {
        $this->lap = 0;
        $this->firstElementHasBeenNexted = false;
        return parent::rewind();
    }
}
