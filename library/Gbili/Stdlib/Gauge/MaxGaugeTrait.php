<?php
namespace Gbili\Stdlib\Gauge;

/**
 * 
 * @author g
 *
 */
trait MaxGaugeTrait
{
    /**
     * Avoid a max count of 0
     *
     * $max === null results in $max set to $initialCount in
     * parent which, here is by default 0... And even if
     * $initialCount were not set to 0 by default, a user
     * could set it to 0.
     *
     * $max === 0 is not permitted
     * @param integer $initialCount
     */
    public function __construct($initialCount = 0, $max = null)
    {
        if (null === $max) {
            throw new Exception('You must set a maximum value');
        }
        
        if (!is_numeric($max) || 0 === (integer) $max) {
            throw new Exception('TypeError or NumberError: $max must be a number different that 0');
        }
        return parent::__construct($initialCount, $max);
    }
    
    /**
     * (non-PHPdoc)
     * @see Gbili\Stdlib\Gauge.Gauge::onRemoveMax()
     */
    public function onRemoveMax()
    {
        throw new Exception('You cannot remove max in Max mode');
    }
    
    /**
     *
     */
    public function reachedLimit()
    {
        return $this->reachedMax();
    }
}