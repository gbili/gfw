<?php
namespace Gbili\Line\LineCollection;

interface CollectionAwareInterface
{
    /**
     * @return \Gbili\Line\LineCollection
     */
    public function getLineCollection();
}