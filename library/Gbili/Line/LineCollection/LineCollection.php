<?php
namespace Gbili\Line\LineCollection;



use Gbili\Stdlib\Collection;
use Gbili\Line\Line;
use Gbili\Line\BlankLine;

class LineCollection extends Collection
{   
    /**
     * Inited to 0 because the first
     * line will be the longest line
     * at that time.
     * 
     * @var number     
     */
    private $longestLineIndex = 0;
    
    /**
     * 
     * @param string|Line $line
     * @throws Exception
     * @return boolean
     */
    public function add($line = null)
    {
        if (null === $line) {
            $line = new BlankLine();
        }
        
        if (is_string($line)) {
            $line = new Line($line);
        }
        
        $this->updateLongest($line);
        return parent::add($line);
    }
    
    /**
     * Check if the line that is GOING TO BE ADDED
     * is longer than the longest line
     * 
     * @param \Gbili\Line\Line $line
     */
    protected function updateLongest(Line $line)
    {
        if ($this->isEmpty() || $line->getLength() > $this->getLongest()->getLength()) {
            $this->longestLineIndex =  $this->count();
        }
    }
    
    /**
     * 
     * @return \Gbili\Line\Line
     */
    public function getLongest()
    {
        if ($this->isEmpty()) {
            throw new Exception("LineCollection is empty, no longest line for now");
        }

        if (!$this->offsetExists($this->longestLineIndex)) {
            throw new Exception("Wrong index for longest line");
        }

        return $this->offsetGet($this->longestLineIndex);
    }
}