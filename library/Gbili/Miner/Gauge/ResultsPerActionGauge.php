<?php
namespace Gbili\Miner\Gauge;

use Gbili\Stdlib\Gauge\Events\EventsMaxGauge;
use Gbili\Miner\AttachableListenersInterface;

/**
 * 
 * @author g
 *
 */
class ResultsPerActionGauge extends EventsMaxGauge implements AttachableListenersInterface
{   
    protected $defaultListeners = array(
        'ApplicationListenerAggregate'
    );
    
    /**
     * 
     * @var number
     */
    protected $actionId = null;
    
    /**
     * 
     * @param number $count
     */
    public function __construct($count)
    {
        parent::__construct(0, (integer) $count);
    }
    
    /**
     * 
     */
    public function getListeners()
    {
        return $this->defaultListeners;
    }
    
    /**
     * Action id is used to only attach the gauge the
     * the event related to the action it is supposed
     * to monitor. (Application, calls en event with 
     * the actionId appended to it)
     * @param number $id
     */
    public function setMonitoredActionId($id)
    {
        $this->actionId = $id;
    }
    
    /**
     * 
     * @throws Exception
     */
    public function getMonitoredActionId()
    {
        if (null === $this->actionId) {
            throw new Exception('You must set the monitored action Id');
        }
        return $this->actionId;
    }
}
