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
     * 
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
     * 
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
     * @return mixed
     */
    public function getSample()
    {
        if ($this->isEmpty()) {
            throw new Exception("Cannot get sample before setting some elements into collection");
        }
        
        if ($this->valid()) {
            return $this->current();
        }
        $this->rewind();
        return $this->getSample();
    }
    
    /**
     * 
     * @return boolean|mixed
     */
    public function getNext()
    {
        $this->next();
        if (!$this->valid()) {
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
    }
    
    /**
     * 
     * @return boolean|mixed
     */
    public function getLast()
    {
        $this->last();
        if (!$this->valid()) {
            return false;
        }
        return $this->current();
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
    public function push(Collection $c)
    {
        foreach ($c as $e) {
            $this->add($e);
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
            throw new Exception("There are no elements, to print an empty string just echo '';");
        }
        $thisArray = iterator_to_array($this, false);
        return array_reduce($thisArray, function ($str, $element) {
            return $str .= ($element instanceof ToStringInterface)? $element->toString() : (string) $element;
        }, '');
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