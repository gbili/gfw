<?php
namespace Gbili\Stdlib;

use ArrayIterator;
use Gbili\Stdlib\ThrowIf\MagicThrow;
use Gbili\Stdlib\ToStringInterface;

class Collection extends ArrayIterator implements ToStringInterface
{
    
    public function __construct(array $array = array())
    {
        parent::__construct($array);
    }
    
    /**
     * Return the key element pair currently being pointed at, 
     * and move the pointer to the next position 
     * Return false if there are already no more elements 
     * before even moving the pointer
     * @return boolean|multitype:Ambigous <\Gbili\Stdlib\mixed, boolean>
     */
    public function each()
    {
        if (false === $this->key()) {
            return false;
        }
        $ret = array($this->key() => $this->current());
        $this->next();
        return $ret;
    }
    
    /**
     * Return the value at current pointer positoin
     * @throws Exception
     */
    public function getCurrent()
    {
        if (!$this->valid()) {
            throw new Exception('Current position is not valid');
        }
        return $this->current();
    }
    
    /**
     * Get a sample element in array
     * Either the value at current position if valid or the first element
     * @return mixed
     */
    public function getSample()
    {
        if ($this->isEmpty()) {
            throw new Exception("Cannot get sample before setting some elements into collection");
        }
        
        if ($this->valid()) {
            $this->getCurrent();
        }
        $cloned = clone $this;
        $cloned->rewind();
        return $cloned->getCurrent();
    }
    
    /**
     * 
     * @return boolean|mixed
     */
    public function getNext()
    {
        $this->next();
        if (!$this->valid()) {
            $this->last(); //move to the last valid position avoid exception
            return false;
        }
        return $this->current();
    }

    /**
     * 
     */
    public function last()
    {
        $this->seek(($this->isEmpty())? 0 : $this->count() - 1);
        if (!$this->valid()) {
            return false;
        }
        return $this->current();
    }
    
    /**
     * 
     * @return boolean|mixed
     */
    public function getLast()
    {
        return $this->last();
    }
    
    /**
     * 
     * @return boolean|mixed
     */
    public function getFirst()
    {
        if ($this->isEmpty()) {
            return false;
        }
        $this->rewind();
        return $this->getCurrent();
    }
    
    /**
     * 
     * @return boolean
     */
    public function isEmpty()
    {
        return 0 === $this->count();
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasSingleElement()
    {
        return 1 === $this->count();
    }
    
    /**
     * 
     * @param string|Element $element
     * @throws Exception
     * @return boolean
     */
    public function add($element)
    {
        parent::append($element);
        return $this;
    }
    
    /**
     * 
     * @return multitype:
     */
    public function get($index = null)
    {
        return parent::offsetGet($index);
    }
    
    /**
     * 
     * @param Collection $c
     */
    public function merge(Collection $c)
    {
        $c->rewind();
        while ($c->valid()) {
            $k = $c->key();
            $e = $c->current();
            $this->add($e);
            $c->next();
        }
        return $this;
    }

    /**
     * 
     * @param number $index
     * @return boolean
     */
    public function has($index)
    {
        if ($this->isEmpty()) {
            throw new Exception("There are no elements in collections, so the index hardly exists, add elements...");
        }
        return parent::offsetExists($index);
    }
    
    /**
     * This one can throw up as many times as it wants
     * Allows us to get a stack trace
     * @return string
     */
    public function toString()
    {
        if ($this->isEmpty()) {
            return '';
        }
        $thisArray = iterator_to_array($this, false);
        return array_reduce($thisArray, function ($str, $element) {
            return $str .= ($element instanceof ToStringInterface)? $element->toString() : (string) $element;
        }, '');
    }

    public function debugString()
    {
        $kvAsStrings = [];
        foreach ($this as $k => $v) {
            $kvAsStrings[] = '{'. $k . ':' . $v . '}';
        }
        return implode(", \n", $kvAsStrings);
    }
    
    /**
     * 
     * @return string
     */
    public function __toString()
    {
        return implode('', iterator_to_array($this, false));
    }
}
