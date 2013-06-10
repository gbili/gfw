<?php
namespace Gbili\Vid;

use Gbili\Db\Req\AbstractInstaller;

class Installer extends AbstractInstaller
{
    public function getRegexMatchingTableNames()
    {
        return '#Gbili_(?:Vid)|(?:Source)#';
    }
    
    public function getBaseDir()
    {
        return __DIR__;
    }
}