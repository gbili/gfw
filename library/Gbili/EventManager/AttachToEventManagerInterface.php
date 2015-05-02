<?php
namespace Gbili\EventManager;

/**
 * Some event manager aware classes may chose to call this on a potential 
 * listener implementing this interface
 * this allows listeners to attach to the callers event manager
 */
interface AttachToEventManagerInterface
{
    public function attachToEventManager(\Zend\EventManager\EventManagerInterface $em);
    public function detachFromEventManager(\Zend\EventManager\EventManagerInterface $em);
}
