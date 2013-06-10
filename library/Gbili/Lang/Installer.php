<?php
namespace Gbili\Lang;

use Gbili\Db\Req\AbstractInstaller;

class Installer extends AbstractInstaller
{
    public function getRegexMatchingTableNames()
    {
        return '#Lang(ISO|Dirty)#';
    }
    
    public function getBaseDir()
    {
        return __DIR__;
    }
}