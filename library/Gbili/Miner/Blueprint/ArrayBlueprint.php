<?php
namespace Gbili\Miner\Blueprint;

class ArrayBlueprint
extends AbstractBlueprint
implements BlueprintInterface
{
    public function __construct(\Zend\ServiceManager\ServiceManager $sm)
    {
        $this->setServiceManager($sm);
    }

    public function init()
    {
        $sm = $this->getServiceManager();
        $appConfig = $sm->get('ApplicationConfig');

        if (isset($appConfig['listeners']['action'])) {
            //@TODO Shared event manager is needed to avoid duplicating listeners to actions
            //not concerned by them. Listeners for actions have ids
            //Use lisenersAttacher, because it has some implementation to prefetch 
            //the event callback when it is a string from the service manager
        }

        $puzzleKeys = array(
            'actionId'                     => 'id',
            'parentId'                     => 'parent',
            'isNewInstanceGeneratingPoint' => 'new_instance_generating_point',
            'isOpt'                        => 'optional',
            'title'                        => 'id',
            'data'                         => 'data',
            'inputGroup'                   => 'input_group',
            'useMatchAll'                  => 'match_all',
        );
        foreach ($appConfig['action_set'] as $id => $actionConfig) {
            if (!isset($actionConfig['type'])) {
                throw new \Exception('You must choose between one type of action or the other');
            }
            $actionConfig['match_all'] = isset($actionConfig['match_all']) ? $actionConfig['match_all'] : false;
            $actionConfig['input_group'] = isset($actionConfig['input_group']) ? $actionConfig['input_group'] : 0;
            $actionConfig['id'] = $id;
            $newAction = $sm->get('Action' . (!isset($actionConfig['parent'])?'Root':'') . $actionConfig['type']);
            $newAction->setBlueprint($this);
            $newAction->hydrate($this->arrayPuzzle($puzzleKeys, $actionConfig));
            $this->addAction($newAction);
        }
    }

    public function arrayPuzzle(array $keepKeys, array $keepValues)
    {
        $keep = array();
        foreach ($keepKeys as $key => $keepValuesKey) {
            if (isset($keepValues[$keepValuesKey])) {
                $keep[$key] = $keepValues[$keepValuesKey];
            }
        }
        return $keep;
    }
}
