<?php
namespace Gbili\Tests\Stdlib;

class CircularCollectionTest extends \Gbili\Tests\GbiliTestCase
{
    public function testArrayIsEmptyIfNoParamInConstruction()
    {
        $col = new \Gbili\Stdlib\CircularCollection;
        $this->assertEquals($col->isEmpty(), true);
    }

    public function testArrayGetsSetOnConstruction()
    {
        $col = new \Gbili\Stdlib\CircularCollection(array('1'));
        $this->assertEquals($col->isEmpty(), false);
        $this->assertEquals($col->current(), '1');
    }
}
