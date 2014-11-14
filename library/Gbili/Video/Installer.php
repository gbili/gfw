<?php
namespace Gbili\Video;

use Gbili\Db\Req\AbstractInstaller;

class Installer extends AbstractInstaller
{
    public function getRegexMatchingTableNames()
    {
        return '#(VideoEntity)|(Genre)#';
    }
    
    public function getBaseDir()
    {
        return __DIR__;
    }
}