<?php
namespace Gbili\Miner;

/**
 *
 * Trying to avoid circular dependencies from
 * bootstraping in a service manager initializer
 * This should be called after objects have been
 * referenced in the service manager
 * 
 * @author g
 *
 */
class ListenersAttacher implements \Zend\ServiceManager\ServiceManagerAwareInterface
{
    /**
     * Objects having listeners wanting to attach to them
     * @var array
     */
    protected $attachables = array();
    
    protected $processedAttachables = array();
    
    /**
     * From where to retrieve the listeners
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceManager;

    public function getServiceManager()
    {
        if (null === $this->serviceManager) {
            throw new \Exception('Missing service manager');
        }
        return $this->serviceManager;
    }

    public function setServiceManager(\Zend\ServiceManager\ServiceManager $sm)
    {
        $this->serviceManager = $sm;
        return $this;
    }

    /**
     * 
     * @param AttachableListenerInterface $al
     */
    public function registerAttachable(HasAttachableListenersInterface $al)
    {
        if (!$al instanceof \Zend\EventManager\EventManagerAwareInterface) {
            throw new Exception('registered instance having attachable listeners, must be an instanceof EventManagerAwareInterface');
        }
        $this->addUniqueAttachable($al);
    }

    /**
     * Do not add the same object twice
     */
    protected function addUniqueAttachable($al)
    {
        foreach ($this->attachables as $attachable) {
            if ($attachable === $al) {
                return; 
            }
        }
        $this->attachables[] = $al;
    }

    /**
     * Attach listeners to their attachables
     */
    public function attach()
    {
        while (!empty($this->attachables)) {
            $attachable = array_shift($this->attachables);
            $this->attachListenersToAttachable($attachable);
            $this->processedAttachables[] = $attachable;
        }
    }

    /**
     * 
     * @param unknown_type $attachable
     */
    public function attachListenersToAttachable(HasAttachableListenersInterface $attachable)
    {
        $events = $attachable->getEventManager();
        $listenersAsAggregates = array_filter($attachable->getListeners(), function ($value) { return is_string($value); });
        foreach (array_unique($listenersAsAggregates) as $listenerAggregateIdenfier) {
            $events->attach($this->serviceManager->get($listenerAggregateIdenfier));
        }
    }
}
