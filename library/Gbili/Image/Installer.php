<?php
namespace Gbili\Image;

use Gbili\Db\Req\AbstractInstaller;

class Installer extends AbstractInstaller
{
    public function getRegexMatchingTableNames()
    {
        return '#(Image)#';
    }
    
    public function getBaseDir()
    {
        return __DIR__;
    }
}