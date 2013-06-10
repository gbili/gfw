<?php
namespace Gbili\Stdlib;

/**
 * This class is meant group the data needed to apply a variable
 * delay to the script execution. It may be useful to set an HTTP
 * requests.
 * 
 * @author g
 *
 */
class Delay
{
    /**
     * 
     * @var number
     */
    protected $min;
    
    /**
     * 
     * @var number
     */
    protected $max;
    
    /**
     * 
     * @var number
     */
    protected $delay;
    
    /**
     * 
     * @param number $min
     * @param number $max
     * @throws Exception
     */
    public function __construct($min, $max=null)
    {
        if (!is_numeric($min) || (null !== $max && !is_numeric($max) || ($min > $max))) {
            throw new Exception('min and max must be numeric and max > min');
        }
        $this->min = (integer) $min;
        $this->max = (integer) (null !== $max)? $max : $min;
    }
    
    /**
     * 
     * @return \Gbili\Stdlib\Delay
     */
    public function apply()
    {
		sleep($this->getDelay());
		return $this;
    }

    /**
     * 
     */
    public function getDelay()
    {
        if (null === $this->delay) {
            $this->reset();
        }
        return $this->delay;
    }

    /**
     * 
     * @return \Gbili\Stdlib\Delay
     */
    public function reset()
    {
        $this->delay = rand($this->min, $this->max);
        return $this;
    }
}