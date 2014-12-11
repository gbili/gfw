<?php
namespace Gbili\Miner\Application;

class FlowEvaluator
{
    /**
     * @var boolean
     */
    const EXECUTE = 1;

    /**
     * @var
     */
    const FAIL = 2;

    /**
     * @var
     */
    const FLOW_END = 3;

    protected $thread;
    
    /**
     * 
     * @param Thread $t
     */
    public function __construct(Thread $t)
    {
        $this->thread = $t;
    }
    
    /**
     *                                                                  
     *     ---/==============> Execute (Place Next Result)                          
     *    /  /                     |                                
     *   /  /                 1.Success?                           
     *  /  /                 /          \ (false)
     * /  /                 /            v 
     * |  |          (true)/            4.Is Optional?               
     * |  |               /           (true)/  (false)\
     * |  |              v                 v           \
     * |  |     2.Has More Children?<__GOTO Parent      \________> could call attemptFailRecovery()          
     * |  |    (true)/    (false)\              ^   
     * |  |         v             v              \   
     * ---|-GOTO Next Child   3.Has More Results? \                    
     *    |______________________/(true) (false)\__|     
     *                                                         
     */
    public function evaluate()
    {
        $action = $this->thread->getAction();
        
        if (!$action->executionSucceed()) {//1.
            if ($action->isRoot() || (!$action->isOptional())) { //4.
                return self::FAIL;
            }
            $this->thread->retakeFlowFromParent();
            return $this->evaluate();
        }
        
        $childrenCollection = $action->getChildrenCollection();
        
        if (!$childrenCollection->isEmpty()) {//2.
            $childrenCollection->getNext();
            if (!$childrenCollection->hasChangedLap()) {
                $this->thread->placeChildIntoFlow();
                return self::EXECUTE;
            }
        }
        
        if (!$action->hasMoreResults()) {//3.
            if ($action->isRoot()) { //4.
                return self::FLOW_END;
            }
            $this->thread->retakeFlowFromParent();
            return $this->evaluate();
        }
        
        $this->thread->placeSameAction();//Aka execute next result
        return self::EXECUTE;
    }
    
    public function attemptFailRecovery()
    {
        //@todo but what happens if for example it is a connection problem for get contents? those errors should be managed differently no?
        $this->thread->retakeFlowFromParent();
        return $this->evaluate();
    }
}
