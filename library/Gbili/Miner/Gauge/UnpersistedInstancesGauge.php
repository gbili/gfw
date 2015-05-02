<?php
namespace Gbili\Miner\Gauge;

use Gbili\Stdlib\Gauge\Events\EventsMaxGauge;
use Gbili\Miner\HasAttachableListenersInterface;

/**
 * 
 * @author g
 *
 */
class UnpersistedInstancesGauge extends EventsMaxGauge implements HasAttachableListenersInterface
{
    protected $defaultListeners = array(
        'PersistanceListenerAggregate'
    );
    
    /**
     * 
     * @param integer $max
     */
    public function __construct($max)
    {
        return parent::__construct(0, $max);
    }
    
    /**
     * 
     */
    public function getListeners()
    {
        return $this->defaultListeners;
    }
}
