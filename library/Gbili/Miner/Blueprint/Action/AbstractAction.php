<?php
namespace Gbili\Miner\Blueprint\Action;

/**
 * This will contain an object representation of
 * the action table set.
 * The actions will inject themselves the input data
 * from their parents
 * They will also know what part of the result means
 * what, and will make the final results (if there is any)
 * be available from the public method spit().
 * 
 * @author gui
 */
abstract class AbstractAction
implements \Zend\EventManager\EventManagerAwareInterface
{
    use \Zend\EventManager\EventManagerAwareTrait;

	const EXECUTION_SUCCESS = true;
	const EXECUTION_FAIL    = false;

    /**
     * @var array used to identify the eventis in shared events
     */
    protected $eventIdentifiers;

    /**
     * Needed in extract action for input group
     * @var array
     */
    protected $hydrationInfo;

	/**
	 * 
	 * @var integer 
	 */
	private $id;
	
	/**
	 * 
	 * @var \Gbili\Miner\BlueprintInterface
	 */
	private $blueprint;
	
	/**
	 * Lets the Miner_Persistance_Persistance know whether
	 * it has to create a new instance of
	 * Miner_Persistance::$instancesClassname
	 * 
	 * @todo integrte it
	 * @var unknown_type
	 */
	private $isNewInstanceGeneratingPoint = false;
	
	/**
	 * Keep a pointer of the root action
	 * so it can be accessed quicker
	 * than by doing a loop and calling 
	 * getParent on all the actions chain...
	 * 
	 * @var AbstractAction
	 */
	protected $rootAction;
	
	/**
	 * Points to the parent action which
	 * may be itself in case it is the root
	 * 
	 * @var AbstractAction
	 */
	private $parentAction = null;
	
	/**
	 * 
	 * @var \Gbili\Stdlib\CircularCollection
	 */
	protected $childActionsCollection = null;
	
	/**
     * An action can take input from different
     * actions: either parent or a child action.
     * Input groups are stored as actionId -> inputGroup
	 * 
	 * @var integer
	 */
	protected $inputGroupByInputAction = array();

    /**
     * Contains the input action when not parent
     *
     * @var AbstractAction
     */
    protected $inputAction;
	
	/**
	 * 
	 * @var unknown_type
	 */
	protected $isOpt = false;
	
	/**
	 * Gives humans some insight about the action
	 *
	 * @var unknown_type
	 */
	private $title = null;
	
	/**
	 * You can test whether execution
	 * happened or not if null, and
	 * also if it succeed on bool
	 * 
	 * @var bool|null
	 */
	protected $executionSucceed = null;
	
	/**
	 * This will be saved by the engine
	 * in case it is the new instance
	 * generating point to let him know
	 * where to start the dumping process
	 * next time
	 * 
	 * It does never get cleared, so
	 * if the parent action gives some
	 * input it should allways be set
	 * 
	 * 
	 * @var unknown_type
	 */
	protected $lastInput = null;
	
	/**
	 * 
	 * @var boolean
	 */
	protected $injectsParent = false;
	
	/**
	 * 
	 * @var unknown_type
	 */
	protected $actionInput;

    /**
     * This is how listeners can attach to a specific action identified
     * by its id
     * attach(actionId, eventName, callback, priority)
     */
    protected function loadEventManagerIdentifiers()
    {
        if (null === $this->id) {
            throw new \Exception('Make sure you set the id before calling this');
        }
        $fullSubclass = get_class($this);
        $subclassnameParts = explode('\\', $fullSubclass);
        $subclassname = end($subclassnameParts);
        $this->eventIdentifiers = [
            $this->getId(), //"some id with spaces ok"
            implode('.', [$subclassname, $this->getId()]), //"Extract.some id with spaces ok"
        ];
        $this->getEventManager()->addIdentifiers($this->eventIdentifiers);
    }
	
	/**
	 * 
	 * @param unknown_type $id
	 * @return unknown_type
	 */
	public function setId($id)
	{
		$this->id = $id;
        $this->loadEventManagerIdentifiers();
        return $this;
	}
	
	/**
	 * Give some human insight about the action
	 * 
	 * @param unknown_type $title
	 * @return unknown_type
	 */
	public function setTitle($title)
	{
		$this->title = (string) $title;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasTitle()
	{
		return '' !== $this->title;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getId()
	{
		if (null === $this->id) {
			throw new Exception('there is no id set for this action');
		}
		return $this->id;
	}
	
	/**
	 * Returns the action that should 
	 * be used as input when available
	 * 
	 * @return AbstractAction 
	 */
	public function getInputAction()
	{
        return (null === $this->inputAction || (!$this->inputAction->isExecuted()))
            ? $this->getParent() 
            : $this->inputAction;
	}
	
	/**
     * An action can take input from parent by default
     * or from another action. inputAction holds the
     * other action.
     * The inputGroupByInputAction holds input group
     * by actionId.
	 * 
	 * @return unknown_type
	 */
	public function setInputActionInfo(AbstractAction $action, $inputGroup = null)
	{
        if ($action !== $this->getParent()) {
            $this->inputAction = $action;
        }
        if (null !== $inputGroup && '' !== $inputGroup) {
            $this->inputGroupByInputAction[$action->getId()] = is_numeric($inputGroup)? (integer) $inputGroup : $inputGroup;
        }
	}

    /**
     *
     * @throws Exception when not set
     * @return mixed:null|integer
     */
    public function getInputGroup(AbstractAction $action=null)
    {
        if (null === $action) {
            $action = $this->getInputAction();
        }
        $id = $action->getId();
        if (isset($this->inputGroupByInputAction[$id])) {
            return $this->inputGroupByInputAction[$id];
        }
        return null;
    }

    /**
     * Tells whether the input group has been set
     */
    public function hasInputGroup()
    {
        return $this->getInputGroup() !== null;
    }

    /**
     * Try to get input from action
     *
     * @var 
     */
    public function getInputFromAction()
    {
	    if (!$this->getInputAction()->canGiveInputToAction($this)) {
            if (!$this->isOptional()) {
                throw new \Exception(
                    'Action ' . get_class($this) . ' : ' . $this->getTitle() . ' InputGroup: ' . print_r($this->getInputGroup(), true) . "\n"
                    . 'Parent' . get_class($this->getParent()) . ': ' . $this->getParent()->getTitle() . "\n"
                    . 'Referring to an input group: ' . var_export($this->getInputGroup()) .  ' that does not exist in extract parent resultset');
            }
            return false;
	    }
    	
		$input = $this->getInput();

        $this->getEventManager()->trigger(
            'executeInput',
            $this,
            [
                'input' => $input,
            ]
        );

        return $input;
    }
	
	/**
	 * 
	 * @return \Gbili\Miner\BlueprintInterface
	 */
	public function getBlueprint()
	{
		if (null === $this->blueprint) {
			throw new Exception('Blue print was not set');
		}
		return $this->blueprint;
	}
	
	/**
	 * 
	 * @param \Gbili\Miner\BlueprintInterface $b
	 * @return unknown_type
	 */
	public function setBlueprint(\Gbili\Miner\Blueprint\BlueprintInterface $b)
	{
		$this->blueprint = $b;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function getLastInput()
	{
		return $this->lastInput;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function hasLastInput()
	{
		return null !== $this->lastInput;
	}
	
	/**
	 * This function should only be called from within the setChildAction function
	 * 
	 * @param \Gbili\Miner\Blueprint\Action\AbstractAction $action
	 */
	public function setParent(AbstractAction $action)
	{
		$this->parentAction = $action;
	}
	
	/**
	 * 
	 * @return \Gbili\Miner\Blueprint\Action\AbstractAction
	 */
	public function getParent()
	{
		if (null === $this->parentAction) {
			throw new Exception('there is no parent action set right now, call setParent() before calling getParent()');
		}
		return $this->parentAction;
	}
	
	/**
	 * 
	 * @param \Gbili\Miner\Blueprint\Action\RootActionInterface $action
	 */
	public function setRoot(\Gbili\Miner\Blueprint\Action\RootActionInterface $action)
	{
		$this->rootAction = $action;
	}
	
	/**
	 * Returns the pointer to the root action
	 * 
	 * @return \Gbili\Miner\Blueprint\Action\RootActionInterface
	 */
	public function getRoot()
	{
		if (null === $this->rootAction) {
			throw new Exception('The root action is not set');
		}
		return $this->rootAction;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function isRoot()
	{
	    return $this->getRoot() === $this;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function isExecuted()
	{
		return null !== $this->executionSucceed;
	}
	
	/**
	 * 
	 * @throws Exception
	 * @return boolean
	 */
	public function executionSucceed()
	{
	    if (!$this->isExecuted()) {
	        throw new Exception('You must execute before checking this, action Id: ' . $this->getId());
	    }
	    return $this->executionSucceed;
	}
	
	/**
	 * Allow multiple child actions per action
	 * There may be many child actions per parent
	 * so $this->childrenActionStack can be an array
	 * of AbstractAction or just
	 * a AbstractAction
	 * 
	 * @return unknown_type
	 */
	public function addChild(AbstractAction $action)
	{
		//ad child to stack
        $this->getChildrenCollection()->add($action);
		//also set the parent and root of the $action in parameter
		$action->setParent($this);
		$action->setRoot($this->getRoot());
	}
	
	/**
	 * 
	 * @return \Gbili\Stdlib\CircularCollection
	 */
	public function getChildrenCollection()
	{
	    if (null === $this->childActionsCollection) {
	        $this->childActionsCollection = new \Gbili\Stdlib\CircularCollection();
	    }
	    return $this->childActionsCollection;
	}
	
	/**
	 * @return AbstractAction
	 */
	public function getChild()
	{
	    return $this->getChildrenCollection()->getCurrent();
	}
	
	/**
	 * Tell whether engine should create new video entity
	 * instance from this action
	 * @TODO the new instance generatig point has a flaw when it is attached to an Extract action with matchAll
	 * we have to hook someplace the new instance generation so that there is an instance for every match in matchall
	 * 1. find at which point matchAll can give a hint on the numer of results.
	 * 2. once we have the number, add some sort of communication between the Extract action, and the Persistance::manageNIGP()
	 * 3. make sure that all those instances are saved gracefully. 
	 * 
	 * @return boolean
	 */
	public function isNewInstanceGeneratingPoint()
	{
		return $this->isNewInstanceGeneratingPoint;
	}
	
	/**
	 * Set the video entity starting point to true
	 * @return unknown_type
	 */
	public function setAsNewInstanceGeneratingPoint()
	{
		$this->isNewInstanceGeneratingPoint = true;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function setAsOptional($bool)
	{
		$this->isOpt = (boolean) $bool;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function isOptional()
	{
		return $this->isOpt;
	}
	
	/**
	 * Returns the result of the action
	 * 
	 * @param $groupNumber if the result is divided into groups, this specifies which group to return
	 * @return unknown_type
	 */
	abstract public function getResult($group = null);
	
	/**
	 * Tells the engine whether to call spitt() or not
	 * 
	 * @return unknown_type
	 */
	abstract public function hasFinalResults();
	
	/**
	 * Tells whether we can call execute again or not
	 * @return boolean
	 */
	abstract protected function innerHasMoreResults();

	/**
	 * 
	 * @return boolean
	 */
	public function hasMoreResults()
    {   
        $isExecuted = $this->isExecuted();
        $innerHasMoreResults = $this->innerHasMoreResults();

        // Allow listeners to tell whether an action has more
        // results or not... This can mess the execution flow
        // if not handled properly down the road; like if you
        // tell there are more results when there aren't and
        // you don't provide the additional results by listeninig
        // to getResult.
        $responses = $this->getEventManager()->trigger(
            'hasMoreResults',
            $this,
            [
                'isExecuted' => $isExecuted,
                'hasMoreResults' => $innerHasMoreResults,
            ],
            function ($listenerReturn) {return !$listenerReturn;} //Stop using results of this executed action
        );

        if ($responses->stopped()) {
            return $responses->last();
        }

        return $isExecuted && $innerHasMoreResults;
	}
	
	/**
	 * Once executed, if the action has final results it will
	 * return them as an assciative array
	 * ex : array(VideoName=>'the big lebowsky')
	 * otherwise it will return false
	 * @return unknown_type
	 */
	abstract public function spit();
	
	/**
	 * Will make the results available to the instance
	 * an will return true or false on success or fail
	 * 
	 * @return boolean
	 */
	abstract protected function innerExecute();
	
	/**
	 * The input can come from three different places
	 * -other action than parent
	 * -lastInput (in case there is a cw) : $lastInput 
	 * -parent : $inputGroup
	 * 
	 * The normal action flow is that the roots gets input from bostrapInputData
	 * then it executes, and the result is made available for the children
	 * Then the child executes and so on.
	 * However there may be cases, where some action will need to take
	 * input from a child action so it can create more results. That's when
	 * the flow changes for some loops, until the child cannot generate more
	 * results. :/
	 * 
	 * @return unknown\type
	 */
	public function getInput()
	{
        $responses = $this->getEventManager()->trigger(
            'input', //event identifier
            $this,
            ['lastInput' => $this->lastInput],
            function ($listenerReturn) {return is_string($listenerReturn);} //Meets our expected result, will setStopped
        );
        if ($responses->stopped()) {
            return $responses->last();
        }

        //Input from action
        $input = $this->getInputAction()->giveInputToAction($this);

        //Allow other input action refactoring
        $responses = $this->getEventManager()->trigger(
            'inputFromAction', //event identifier
            $this, //targed
            ['input' => $input], // params
            function ($listenerReturn) {return is_string($listenerReturn);} //Meets our expected result, will setStopped
        );
        if ($responses->stopped()) {
            $input = $responses->last();
        }

		return $input;
	}
	
	/**
	 * 
	 * 
	 * @return mixed:bool|PlaceNextInterface
	 */
	final public function execute()
	{
        $this->getEventManager()->trigger(
            __FUNCTION__ . '.pre',
            $this,
            array('identifiers' => $this->getEventManager()->getIdentifiers())
        );

		$this->executionSucceed = $this->innerExecute();

        $this->getEventManager()->trigger(
            __FUNCTION__ . '.post',
            $this,
            array('status' => $this->executionSucceed)
        );
		// Make sure lastInput was set by subclass
		// after execution
		if ($this->executionSucceed() && null === $this->lastInput) {
		    throw new Exception("Subclasses should keep track of lastInput by setting it after execution");
		}

		return $this->executionSucceed();
	}

    abstract public function canGiveInputToAction(AbstractAction $action);
    abstract public function giveInputToAction(AbstractAction $action);
	
	/**
	 * Clear will empty $results so new info is considered
	 * However if the $results is an array from where different
	 * video entities have to take information, it will only shift
	 * the first result
	 * 
	 * @note make sure to call clear from the last child
	 */
	public function clear()
	{
		if (!$this->isExecuted()) {
			throw new Exception('You cannot clear the instance it has not been executed already call execute().');
		}
		$this->executionSucceed = null;

        $this->innerClear();
        //Allow callbacks and stuff to rewind the loop and stuff
        $this->getEventManager()->trigger(
            __FUNCTION__, //event identifier
            $this,
            []
        );
		
		if (!$this->getChildrenCollection()->isEmpty()) {
    		$this->getChildrenCollection()->rewind();
		}
	}
	
	/**
	 * 
	 * @return \Gbili\Miner\ExecutionStep\AbstractExecutionStep
	 */
	abstract protected function innerClear();
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function clearMeAndOffspring()
	{
		if ($this->hasChildren()) {
			foreach ($this->getChildren() as $child) {
				$child->clearMeAndOffspring();
			}
		}
		return $this->clear();
	}
	
	public function toString()
	{
	    $optional = $this->isOptional()? "yes": "no";
        $class = get_class($this);
	    $str  = "Action Type : {$class}\n";
	    $str .= "Id          : {$this->getId()}\n";
	    $str .= "Title       : {$this->getTitle()}\n";
	    $str .= "Optional    : $optional\n";
	    $str .= "Input       : {$this->getInput()}\n";
	    return $str;
	}

    public function getHydrationInfo()
    {
        return $this->hydrationInfo;
    }

    /**
     * @param $info array
     * @return void 
     */
    public function hydrate(array $info)
    {
        $this->hydrationInfo = $info;
        $this->setId($info['actionId']);
        if (isset($info['parentId']) && $this->getBlueprint()->hasAction($info['parentId']) && !($isRoot = ($info['parentId'] === $info['actionId']))) {
            $parent = $this->getBlueprint()->getAction($info['parentId']);
            $parent->addChild($this);
        }
        if (isset($info['title'])) {
            $this->setTitle($info['title']);
        }
        if (isset($info['isOpt'])) {
            $this->setAsOptional($info['isOpt']);
        }
        if (isset($info['isNewInstanceGeneratingPoint']) && $info['isNewInstanceGeneratingPoint']) {
	        $this->setAsNewInstanceGeneratingPoint();
        }
    }
}
