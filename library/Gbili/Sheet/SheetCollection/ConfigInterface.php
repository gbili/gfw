<?php
namespace Gbili\Sheet\SheetCollection;

interface ConfigInterface
{
    public function getConfig();
    public function setConfig(array $config);
    public function configureSheetCollection(SheetCollection $sc);
}