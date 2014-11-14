<?php
namespace Gbili\Miner\Persistance;

class PersistableXMLWriter extends \XMLWriter implements PersistableInterface
{
    public function persist()
    {
        return parent::flush();
    }
}