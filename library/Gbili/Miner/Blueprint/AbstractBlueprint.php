<?php
namespace Gbili\Miner\Blueprint;

/**
 * The Blueprint takes a host as constructor parameter and with that it will
 * try to reconstruct a previously saved (with \\Gbili\\Miner\\Blueprint_Savable)
 * action tree. For that it queries the storage (for example the database) using the
 * DbRegistry which must return an instanceof \\Gbili\\Miner\\Blueprint_Db_Interface.
 * @see DbRegistry
 * The returned instance contains all the information the blueprint needs to create
 * an action tree. The action tree may contain two types of actions :
 * 1) Extract
 * 		Extracts bits (parts) of data from the plain text other actions pass to it.
 * 		It takes input either from Extract or GetContents actions.
 * 2) GetContents
 * 		Gets the text from the web, given a string url.
 * 		It takes its input from root data or Extract actions.
 * The blueprint constructs this tree
 * 
 * 
 * 
 * @author gui
 *
 */
abstract class AbstractBlueprint
implements BlueprintInterface
{
	/**
	 * The type of action
	 * Extract
	 * 
	 * @var integer
	 */
	const ACTION_TYPE_EXTRACT = 12;
	
	/**
	 * The type of action
	 * GetContents
	 * 
	 * @var integer
	 */
	const ACTION_TYPE_GETCONTENTS = 13;
	
	/**
	 * 
	 * @var \Zend\ServiceManager\ServiceManager
	 */
	protected $serviceManager;
	
	/**
	 * this is a flat representation of the
	 * actions tree
	 * So it eases access to actions
	 * 
	 * @var unknown_type
	 */
	protected $actions = array();

	/**
	 * @param \Gbili\Url\Authority\Host $host
	 * @return unknown_type
	 */
	public function __construct(\Zend\ServiceManager\ServiceManager $sm)
	{
	    $this->setServiceManager($sm);
	}

    public function setServiceManager(\Zend\ServiceManager\ServiceManager $sm)
    {
        $this->serviceManager = $sm;
        return $this;
    }

    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    public function hasAction($id)
    {
        return isset($this->actions[$id]);
    }

	public function getAction($id)
    {
        if (!$this->hasAction($id)) {
            throw new Exception('No action with thid id: '. $id);
        }
        return $this->actions[$id];
    }

    public function getActions()
    {
        return $this->actions;
    }
	
    /**
     * Add action to action stack indexed by their ids
     * and point the current action to the latest added action
     */
    public function addAction(Action\AbstractAction $action)
    {
	    $this->actions[$action->getId()] = $action;
    }
	
	/**
	 * Proxy
	 * 
	 * @return Blueprint\Action\RootActionInterface
	 */
	public function getRoot()
	{
        if (empty($this->actions)) {
		  	throw new Exception('There are no actions in blueprint, call init()');
		}
        reset($this->actions);
		return current($this->actions)->getRoot();
	}
}
