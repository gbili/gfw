<?php
namespace Gbili\Miner\Blueprint;

interface BlueprintInterface
{
	public function __construct(\Zend\ServiceManager\ServiceManager $sm);

	public function getServiceManager();

	public function setServiceManager(\Zend\ServiceManager\ServiceManager $sm);

    public function hasAction($id);

	public function getRoot();

	public function getAction($id);

    public function getActions();
}
