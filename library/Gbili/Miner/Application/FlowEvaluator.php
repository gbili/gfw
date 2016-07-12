<?php
namespace Gbili\Miner\Application;

class FlowEvaluator
{
    protected $thread;

    /**
     * Initialize it with true so root can execute
     * @var boolean
     */
    protected $canExecuteActionInFlow = true;
    
    /**
     * 
     * @param Thread $t
     */
    public function __construct(Thread $t)
    {
        $this->setThread($t);
    }

    /**
     *
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     *
     */
    public function setThread(Thread $t)
    {
        $this->thread = $t;
        return $this;
    }

    /**
     *
     */
    public function getActionInFlow()
    {
        return $this->thread->getAction();
    }

    /**
     * After action is executed there needs to be a
     * flow evaluation to check whether action in flow
     * can be executed.
     * @return boolean
     */
    public function isCanExecuteActionInFlowOutdated()
    {
        return null === $this->canExecuteActionInFlow;
    }

    /**
     * @return boolean should action in flow be executed?
     */
    public function canExecuteActionInFlow()
    {
        if ($this->isCanExecuteActionInFlowOutdated()) {
            throw new Exception('Call FlowEvaluator::evaluate() before FlowEvaluator::executeActionInFlow()');
        }
        return $this->canExecuteActionInFlow;
    }

    protected function updateCanExecuteActionInFlow($bool)
    {
        return $this->canExecuteActionInFlow = (boolean) $bool;
    }

    /**
     * Called once the action has been executed
     * @return self
     */
    protected function outdateCanExecuteActionInFlow()
    {
        $this->canExecuteActionInFlow = null;
        return $this;
    }

    /**
     *
     * @return boolean action execution success:true or fail:false 
     */
    public function executeActionInFlow()
    {
        if (!$this->canExecuteActionInFlow()) {
            throw new Exception('Trying to execute an action that is considered unexecutable as is');
        }
        $executionReturn = $this->getActionInFlow()->execute();
        $this->outdateCanExecuteActionInFlow();
        return $executionReturn;
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
     * |  |     2.Has More Children?<__GOTO Parent      \________> could call attemptResume()          
     * |  |    (true)/    (false)\              ^   
     * |  |         v             v              \   
     * ---|-GOTO Next Child   3.Has More Results? \                    
     *    |______________________/(true) (false)\__|     
     *                                                         
     * @return boolean can action in flow be executed?
     */
    public function evaluate()
    {
        $action = $this->getActionInFlow();
        
        if (!$action->executionSucceed()) {//1.
            if ($action->isRoot() || !($action->isOptional())) { //4.
                return $this->updateCanExecuteActionInFlow(false);
            }
            $this->thread->retakeFlowFromParent();
            return $this->evaluate();
        }
        
        $childrenCollection = $action->getChildrenCollection();
        
        if (!$childrenCollection->isEmpty()) {//2.
            $childrenCollection->getNext();
            if (!$childrenCollection->hasChangedLap()) {
                $this->thread->placeChildIntoFlow();
                return $this->updateCanExecuteActionInFlow(true);
            }
        }
        
        if (!$action->hasMoreResults()) {//3.
            if ($action->isRoot()) { //4.
                return $this->updateCanExecuteActionInFlow(false);
            }
            $this->thread->retakeFlowFromParent();
            return $this->evaluate();
        }
        
        $this->thread->placeSameAction();//Aka execute next result
        return $this->updateCanExecuteActionInFlow(true);
    }

    /**
     * When the flow evaluator evaluate() method cannot find
     * an executable action, attemptResume() will determine
     * if it is because the script has reached an end.
     * Otherwise it will skip the action's branch and retake
     * flow from parent
     * @return bool whether an executable action was placed
     * into flow.
     */
    public function attemptResume()
    {
        $action = $this->getActionInFlow();
        if ($action->isRoot() && !$action->hasMoreResults()) {
            return false; // Reached end of script, nothing else left to do
        }
        //@todo but what happens if for example it is a connection problem
        //for get contents? shouldn't those errors should be managed differently?
        $this->thread->retakeFlowFromParent();
        return $this->evaluate();
    }
}
