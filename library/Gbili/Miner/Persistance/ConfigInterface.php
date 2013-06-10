<?php
namespace Gbili\Miner\Persistance;

use Gbili\Miner\Persistance;

interface ConfigInterface
{
    public function setConfig(array $config);
    public function getConfig();
    public function configurePersistance(Persistance $e);
}