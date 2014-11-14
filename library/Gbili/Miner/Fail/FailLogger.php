<?php
namespace Gbili\Miner\Fail;

use Zend\EventManager\EventInterface;

class FailLogger
{
	/**
	 * 
	 * @param EventInterface $e
	 */
	public function logExecutionFail(EventInterface $e)
	{
	    $action = $e->getParam('action');
        $str = "\n----------------------------------------------\n"
            . "Action execution failed\n" 
            . $action->toString()
            . "\nParent Action was \n"
            . $action->getParent()->toString()
            . "\n----------------------------------------------\n";
        echo $str;
	}
	
	/**
	 * 
	 * @param EventInterface $e
	 */
	public function logPersitanceFail(EventInterface $e)
	{
	    $persistableInstance = $e->getParam('persistableInstance');
	    $exception = $e->getParam('exception');
	    $exception;
	}
}