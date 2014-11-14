<?php
namespace Gbili\Country;

use Gbili\Db\Req\AbstractInstaller;

class Installer extends AbstractInstaller
{
    public function getRegexMatchingTableNames()
    {
        return '#(Country)#';
    }
    
    public function getBaseDir()
    {
        return __DIR__;
    }
}