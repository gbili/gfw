<?php
namespace Gbili;

class ListenerAggregateAttacher
{
    /**
     * from a list of listeners indexed by events identifier
     * (every identifier can have one or more listeners) 
     * listeners are passed as attach params [eventName, callback, priority]
     * if many for an identifier pass [identifier => [[eventName1, callback1, priority1], [eventName1, callback1, priority1], ....]
     * if one for an identifier you can pass [identifier => [eventName1, callback1, priority1]]
     * @param \Zend\EventManager\EventManagerInterface $events
     * @param array $listeners 
     * @return array the attached listeners handles
     */
    public function attachListenersByEventsIdentifier(\Zend\EventManager\EventManagerInterface $events, array $listeners = array())
    {
        $someAttached = false;
        $attachedListeners = [];
        foreach ($events->getIdentifiers() as $identifier) {
            if (isset($listeners[$identifier])) {
                if (is_string(current($listeners[$identifier]))) {
                    $listeners[$identifier] = [$listeners[$identifier]];
                }
                foreach ($listeners[$identifier] as $attachParams) {
                    $attachedListeners[] = call_user_func_array([$events, 'attach'], $attachParams);
                    $someAttached = true;
                }
            }
        }

        if (!$someAttached) {
            throw new Exception('EventManager identifiers not supported. Event Manager Identifiers are: ' . print_r($events->getIdentifiers(), true) . '. Allowed event identifiers: ' . print_r(array_keys($listeners), true) . 'A \Gbili\Miner\HasAttachableListenersInterface implementor specified in EventManager Identifiers, thinks that you want to listen to it. Either remove yourself from the identifier as a defaultListener, or add the identifier and an event to listen to, in your listeners argument.');
        }

        return $attachedListeners;
    }
}
