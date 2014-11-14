<?php
namespace Gbili\Source;

use Gbili\Db\Req\AbstractInstaller;

class Installer extends AbstractInstaller
{
    public function getRegexMatchingTableNames()
    {
        return '#(Source(Quality)|(Type)|(Validator))#';
    }
    
    public function getBaseDir()
    {
        return __DIR__;
    }
}