<?php
namespace Gbili\Miner\Blueprint\Action\GetContents;

use Gbili\Miner\Blueprint\Action\GetContents;

class RootGetContents 
extends GetContents 
implements \Gbili\Miner\Blueprint\Action\RootActionInterface
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
     *
     */
    public function getInput()
    {
        if ($this->isFirstCallToExecuteForRoot()) {
            return $this->getBootstrapDataAndDiscardItForNextExecute();
        }
        return parent::getInput();
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
        parent::innerClear();
    }

    /**
     * When root you dont want to set the parent
     */
    public function hydrate(array $info)
    {
        $info = array_diff_key($info, array_flip(array('parentId')));
        parent::hydrate($info);
		$this->setBootstrapData($info['data']);
    }
}
