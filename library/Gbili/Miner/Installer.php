<?php
namespace Gbili\Miner;

use Gbili\Db\Req\AbstractInstaller;

class Installer extends AbstractInstaller
{
    public function getRegexMatchingTableNames()
    {
        return '#(BAction)|(Blueprint)#';
    }
    
    public function getBaseDir()
    {
        return __DIR__;
    }
}