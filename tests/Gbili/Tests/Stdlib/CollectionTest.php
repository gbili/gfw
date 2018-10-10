<?php
namespace Gbili\Tests\Stdlib;

class CollectionTest extends \Gbili\Tests\GbiliTestCase
{
    public function testArrayIsEmptyIfNoParamInConstruction()
    {
        $col = new \Gbili\Stdlib\Collection;
        $this->assertEquals($col->isEmpty(), true);
    }

    public function testArrayGetsSetOnConstruction()
    {
        $col = new \Gbili\Stdlib\Collection(array('1'));
        $this->assertEquals($col->isEmpty(), false);
        $this->assertEquals($col->current(), '1');
    }

    public function testArrayIsEmpty()
    {
        $col = new \Gbili\Stdlib\Collection(array());
        $this->assertEquals($col->isEmpty(), true);
    }

    public function testArrayIsEmptyReturnsFalseWhenThereAreElements()
    {
        $col = new \Gbili\Stdlib\Collection(array('1'));
        $this->assertEquals($col->isEmpty(), false);
    }

    public function testCurrentRetursSameElementIfCalledConsequently()
    {
        $col = new \Gbili\Stdlib\Collection(array('1', '2'));
        $this->assertEquals($col->getCurrent(), '1');
        $this->assertEquals($col->getCurrent(), '1');
        $this->assertEquals($col->getCurrent(), '1');
    }

    public function testNextAdvancesPointerAndReturnsElement()
    {
        $col = new \Gbili\Stdlib\Collection(array('1', '2', '3'));
        $this->assertEquals($col->getNext(), '2');
        $this->assertEquals($col->getCurrent(), '2');
        $this->assertEquals($col->getNext(), '3');
        $this->assertEquals($col->getCurrent(), '3');
    }

    public function testNextReturnsFalseWhenNoMoreElements()
    {
        $col = new \Gbili\Stdlib\Collection(array('1'));
        $this->assertEquals($col->getNext(), false);
    }

    public function testGetNextDoesNotMoveThePointerToAnInvalidPositionWhenNoMoreElements()
    {
        $col = new \Gbili\Stdlib\Collection(array('1', '2'));
        $this->assertEquals($col->getNext(), '2');
        $this->assertEquals($col->getNext(), false);
        $this->assertEquals($col->getNext(), false);
        $this->assertEquals($col->getCurrent(), '2');
    }

    public function testGetFirstReturnsFirstEventWhenNotCurrentPosition()
    {
        $col = new \Gbili\Stdlib\Collection(array('1', '2'));
        $this->assertEquals($col->getFirst(), '1');
        $this->assertEquals($col->getNext(), '2');
        $this->assertEquals($col->getCurrent(), '2');
        $this->assertEquals($col->getFirst(), '1');
    }

    public function testGetFirstRewindsCollection()
    {
        $col = new \Gbili\Stdlib\Collection(array('1', '2'));
        $this->assertEquals($col->getNext(), '2');
        $this->assertEquals($col->getFirst(), '1');
        $this->assertEquals($col->getCurrent(), '1');
    }

    public function testGetLastReturnsLast()
    {
        $col = new \Gbili\Stdlib\Collection(array('1', '2'));
        $this->assertEquals($col->getLast(), '2');
    }

    public function testGetLastMovesPointerToLastElement()
    {
        $col = new \Gbili\Stdlib\Collection(array('1', '2'));
        $this->assertEquals($col->getLast(), '2');
        $this->assertEquals($col->getCurrent(), '2');
    }

    public function testGetLastAllwaysReturnsTheLastElement()
    {
        $col = new \Gbili\Stdlib\Collection(array('1', '2'));
        $this->assertEquals($col->getLast(), '2');
        $col->getFirst();
        $this->assertEquals($col->getLast(), '2');
        $col->getNext();
        $this->assertEquals($col->getLast(), '2');
        $this->assertEquals($col->getLast(), '2');
    }

    public function testGetSampleReturnsAnElement()
    {
        $col = new \Gbili\Stdlib\Collection(array('1', '2'));
        $this->assertEquals(is_string($col->getSample()), true);
    }

    public function testGetSampleReturnsElementEvenWhenPointerIsAtEndPosition()
    {
        $col = new \Gbili\Stdlib\Collection(array('a', 'b'));
        $this->assertEquals($col->getNext(), 'b');
        $this->assertEquals(is_string($col->getSample()), true);
    }

    public function testGetSampleDoesNotMoveThePointerWhenPointerIsAtEndPosition()
    {
        $col = new \Gbili\Stdlib\Collection(array('a', 'b'));
        $this->assertEquals($col->getNext(), 'b');
        $this->assertEquals(is_string($col->getSample()), true);
        $this->assertEquals($col->getCurrent(), 'b');
    }

    public function testHasSingleElementRetunsFalseWhenEmpty()
    {
        $emptyArray = array();
        $col = new \Gbili\Stdlib\Collection($emptyArray);
        $this->assertEquals(count($emptyArray) !== 1, true);
        $this->assertEquals($col->hasSingleElement(), false);
    }

    public function testHasSingleElementRetunsFalseWhenMoreThanOneElements()
    {
        $multipleElementsArray = array('1', '2');
        $col = new \Gbili\Stdlib\Collection($multipleElementsArray);
        $this->assertEquals(count($multipleElementsArray) !== 1, true);
        $this->assertEquals($col->hasSingleElement(), false);
    }

    public function testHasSingleElementRetunsTrueWhenSingleElement()
    {
        $singleElementArray = array('1');
        $col = new \Gbili\Stdlib\Collection($singleElementArray);
        $this->assertEquals(count($singleElementArray), 1);
        $this->assertEquals($col->hasSingleElement(), true);
    }

    public function testAddAddsTheElementToTheEndOfTheArray()
    {
        $col = new \Gbili\Stdlib\Collection(array());
        $col->add('1');
        $this->assertEquals($col->getLast(), '1');
    }
}
