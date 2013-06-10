<?php
namespace Gbili\Sheet\SheetCollection;

interface CollectionAwareInterface
{
    /**
     * @return SheetCollection
     */
    public function getSheetCollection();
}