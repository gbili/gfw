<?php
namespace Gbili\Line;

class IndexedLine extends Line
{   
    /**
     * 
     * @var integer
     */
    private $index   = null;
    
    /**
     * 
     * @param string $content
     * @param number $index
     * @param number $maxLength
     */
    public function __construct($content = null, $maxLength = null, $index = null)
    {
        parent::__construct($content, $maxLength);
        
        if (null !== $index) {
            $this->setIndex($index);
        }
    }
    
    /**
     * 
     * @param unknown_type $number
     * @return \Gbili\Line
     */
    public function setIndex($number)
    {
        if (!is_numeric($number)) {
            throw new Exception('setIndex($param) must be numeric');
        }
        $this->index = (integer) $number;
        return $this;
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasIndex()
    {
        return null !== $this->index;
    }
    
    /**
     * 
     * @return number
     */
    public function getIndex()
    {
        if (!$this->hasIndex()) {
            throw new Exception("No index set for this line");
        }
        return $this->index;
    }
}