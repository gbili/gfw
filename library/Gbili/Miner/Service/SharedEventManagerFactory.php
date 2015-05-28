<?php
namespace Gbili\Miner\Service;

class SharedEventManagerFactory implements \Zend\ServiceManager\FactoryInterface
{
    protected $serviceManager;

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $sm
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $sm)
    {
        $this->serviceManager = $sm;
        $sharedEvents = new \Zend\EventManager\SharedEventManager;

        $config = $this->serviceManager->get('ApplicationConfig');
        if (isset($config['listeners'])) {
            $listeners = $this->uniformizeListenersConfigArray($config['listeners']);
            $this->attachListenersProvidedAsCallParams($listeners, $sharedEvents);
        }
        return $sharedEvents;
    }

    protected function uniformizeListenersConfigArray($listenersConfig)
    {
        $identifierKeys = array_filter(array_keys($listenersConfig), function ($key) { return is_string($key); });
        if (empty($identifierKeys)) {
            return $listenersConfig;
        }
        $listenersGroupedByIdentifierKey = array_intersect_key($listenersConfig, array_flip($identifierKeys));
        $uniformed = array_diff_key($listenersConfig, $listenersGroupedByIdentifierKey);
        foreach ($listenersGroupedByIdentifierKey as $identifier => $listeners) {
            if (!is_array($listeners)) {
                throw new \Exception('Bad formating, expected array, passed: '. print_r($listeners, true));
            }
            foreach ($listeners as $listener) {
                if (!is_array($listener)) {
                    throw new \Exception('Bad formating, expected array, passed: '. print_r($listener, true));
                }
                array_unshift($listener, $identifier);
                $uniformed[] = $listener;
            }
        }
        return $uniformed;
    }

    protected function attachListenersProvidedAsCallParams($listeners, $sharedEvents)
    {
        $listenersPassedAsAttachCallParams = array_filter($listeners, function ($value) {
            return $this->filterListenersKeepThosePassedAsAttachCallParams($value);
        });
        $listenersPassedAsAttachCallParamsWithCallables = array_map(function ($value) {
            return $this->makeSureCallbackParamContainsCallable($value);
        }, $listenersPassedAsAttachCallParams);

        foreach ($listenersPassedAsAttachCallParamsWithCallables as $paramsArray) {
            call_user_func_array(array($sharedEvents, 'attach'), $paramsArray);
        }
    }

    protected function filterListenersKeepThosePassedAsAttachCallParams($value)
    {
        $minParamsCount = count(array('identifier', 'event', 'callback'));
        $maxParamsCount = count(array('identifier', 'event', 'callback', 'priority'));
        $count = is_array($value)? count($value) : false;
        return $count && $count >= $minParamsCount && $count <= $maxParamsCount; 
    }

    protected function makeSureCallbackParamContainsCallable(array $attachParams)
    {
        $identifierIndex = 0;
        $eventIndex = 1;
        $callbackIndex = 2;
        $priorityIndex = 3;

        $identifier = $attachParams[$identifierIndex];
        $event = $attachParams[$eventIndex];
        $callback = $attachParams[$callbackIndex];

        if (is_string($callback) && $this->serviceManager->has($callback)) {
            $callback = $this->serviceManager->get($callback);
        }
        if (!is_callable($callback)) {
            if (is_string($callback)) {
                throw new \Exception('The service manager does not know about your callback. Did you mispell it? Because it is neither callable:' . print_r($callback, true));
            }
            throw new \Exception('Callback type not supported, either callable or string please: ' . print_r($callback, true) . print_r($attachParams, true));
        }

        if (isset($attachParams[$priorityIndex])) {
            return array($identifier, $event, $callback, $attachParams[$priorityIndex]);
        }
        return array($identifier, $event, $callback);
    }
}
