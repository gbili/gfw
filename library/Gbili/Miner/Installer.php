<?php
namespace Gbili\Miner;

use Gbili\Db\Req\AbstractInstaller;

class Installer extends AbstractInstaller
{
    public function getRegexMatchingTableNames()
    {
        return '#(Callable)|(Content)|(BAction)|(Blueprint)#';
    }
}
