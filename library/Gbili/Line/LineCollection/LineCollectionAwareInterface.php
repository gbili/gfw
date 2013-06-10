<?php
namespace Gbili\Line\LineCollection;

interface LineCollectionAwareInterface
{
    /**
     * @return \Gbili\Line\LineCollection
     */
    public function getLineCollection();
}