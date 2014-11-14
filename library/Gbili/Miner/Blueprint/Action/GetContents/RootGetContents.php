<?php
namespace Gbili\Miner\Blueprint\Action\GetContents;

use Gbili\Miner\Blueprint\Action\RootAction;
use Gbili\Miner\Blueprint\Action\GetContents;
use Gbili\Miner\Blueprint\Action\Flow\End;
use Gbili\Miner\Blueprint\Action\Flow\PlaceMe;


class RootGetContents extends GetContents implements RootAction
{
    /**
     * Contains the input data when the action
     * is the root actio and it cannot retrieve
     * the input from another action.
     *
     * Once used, this var is set to false
     *
     * @var string
     */
    private $bootstrapInputData = null;
    
    /**
     *
     * @param allows to set the input data from construction
     * (usefull from blueprint when root action) $urlString
     * @return unknown_type
     */
    public function __construct()
    {
        $this->setRoot($this);
        $this->setParent($this);
    }
    
    /**
     * 
     * @param unknown_type $urlString
     */
    public function setBootstrapData($urlString)
    {
        $this->bootstrapInputData = $urlString;
    }
    
    /**
     * @todo root should be another class
     *
     * @throws Exception
     */
    public function getInput()
    {
        if ($this->isFirstCallToExecuteForRoot()) {
            return $this->getBootstrapDataAndDiscardItForNextExecute();
        }
        if ($this->needsInputFromOtherThanParent()) {
            return $this->getInputFromAction($this->getBlueprint()->getAction($this->otherInputActionId), $this->otherActionGroupForInputData);
        }
        if ($this->hasCallbackWrapper()) {
            return $this->getInputFromCallback();
        }
        throw new Exception('Root can only get input from 1. bootstrapInput, 2. otherInputActionId (different than parent (would be itself)), 3. callback');
    }
    
    /**
     *
     * @throws Exception
     * @return boolean
     */
    protected function isFirstCallToExecuteForRoot()
    {
        if (null === $this->bootstrapInputData) {
            throw new Exception("It is root and it has no input data when root");
        }
        return false !== $this->bootstrapInputData;
    }
    
    /**
     * Make sure the bootsrapInputData is not used
     * as input more than once:
     * !! After first time, use lastInput
     */
    protected function getBootstrapDataAndDiscardItForNextExecute()
    {
        $ret = $this->bootstrapInputData;
        $this->bootstrapInputData = false;
        return $ret;
    }
    
    /**
     * (non-PHPdoc)
     * @see Blueprint/Miner_Persistance_Blueprint_Action#clear()
     */
    protected function innerClear()
    {
        $this->isExecuted = false;
        $this->result     = null;
    
        if ($this->hasCallbackWrapper()) {
            $this->getCallbackWrapper()->rewindLoop();
            //now, inputDataWhenRoot can also be used as a placeholder for callback output
            //as it is allways used as input when not null, make sure it is null so on next
            //execution the input will be taken from the parent new result ???????? Really??
            $this->bootstrapInputData = null;//empty placeholder
        }
    }
}