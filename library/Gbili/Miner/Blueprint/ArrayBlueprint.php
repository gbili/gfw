<?php
namespace Gbili\Miner\Blueprint;

class ArrayBlueprint
extends AbstractBlueprint
implements BlueprintInterface
{
    public function init()
    {
        $sm = $this->getServiceManager();
        $appConfig = $sm->get('ApplicationConfig');

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
        foreach ($appConfig['action']['rules'] as $id => $actionConfig) {
            if (!isset($actionConfig['type'])) {
                throw new \Exception('You must choose between one type of action or the other');
            }
            $actionConfig['match_all'] = isset($actionConfig['match_all']) ? $actionConfig['match_all'] : false;
            $actionConfig['input_group'] = isset($actionConfig['input_group']) ? $actionConfig['input_group'] : 0;
            $actionConfig['id'] = $id;
            $newAction = $sm->get('Action' . (!isset($actionConfig['parent'])?'Root':'') . $actionConfig['type']);
            $newAction->setBlueprint($this);
            $puzzler = new \Gbili\Stdlib\ArrayPuzzler;
            $newAction->hydrate($puzzler->puzzle($puzzleKeys, $actionConfig));
            $this->addAction($newAction);
        }
    }
}
