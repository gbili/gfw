<?php
namespace Gbili\Tests\Stdlib;

class CircularCollectionTest extends \Gbili\Tests\GbiliTestCase
{
    public function testArrayIsEmptyIfNoParamInConstruction()
    {
        $col = new \Gbili\Stdlib\CircularCollection;
        $this->assertEquals(true, $col->isEmpty());
    }

    public function testArrayGetsSetOnConstruction()
    {
        $col = new \Gbili\Stdlib\CircularCollection(array('1'));
        $this->assertEquals(false, $col->isEmpty());
        $this->assertEquals('1', $col->current());
    }

    public function testLapIs0AfterConstruction()
    {
        $col = new \Gbili\Stdlib\CircularCollection(array('1', '2'));
        $this->assertEquals(0, $col->getLap());
    }

    public function testCallToGetNextReturnsFirstElementAfterConstruction()
    {
        $col = new \Gbili\Stdlib\CircularCollection(array('1', '2'));
        $this->assertEquals('1', $col->getNext());
    }

    public function testConsecutiveCallsToGetNextReturnEveryElementOnSameLap()
    {
        $col = new \Gbili\Stdlib\CircularCollection(array('1', '2', '3', '4'));
        $this->assertEquals('1', $col->getNext());
        $this->assertEquals('2', $col->getNext());
        $this->assertEquals('3', $col->getNext());
        $this->assertEquals('4', $col->getNext());
    }

    public function testCallToGetNextAfterLastElementWasReturnedReturnsFirstElement()
    {
        $firstElement = '1';
        $lastElement = '2';
        $array = array($firstElement, $lastElement);
        $col = new \Gbili\Stdlib\CircularCollection($array);
        $firstLap = $col->getLap();
        
        //next to last element
        foreach ($array as $v) {
            $el = $col->getNext();
            if ($el === $lastElement) {
                break;
            }
        }

        $this->assertEquals($firstElement, $col->getNext());
    }

    public function testCallToGetNextChangedLapIsTrueWhenNextingAfterLastElement()
    {
        $firstElement = '1';
        $lastElement = '2';
        $array = array($firstElement, $lastElement);
        $col = new \Gbili\Stdlib\CircularCollection($array);

        //next to last element
        foreach ($array as $v) {
            $el = $col->getNext();
            if ($el === $lastElement) {
                break;
            }
        }

        //nexting after last element
        $col->getNext();

        $this->assertEquals(true, $col->lastCallToGetNextChangedLap());
    }

    public function testNextingAfterLastElementIncreasesLap()
    {
        $firstElement = '1';
        $lastElement = '2';
        $array = array($firstElement, $lastElement);
        $col = new \Gbili\Stdlib\CircularCollection($array);
        $firstLap = $col->getLap();

        //next to last element
        foreach ($array as $v) {
            $el = $col->getNext();
            if ($el === $lastElement) {
                break;
            }
        }

        //nexting after last element
        $col->getNext();
        $this->assertEquals(++$firstLap, $col->getLap());
    }

    public function testNextLapIncrementsLap()
    {
        $firstElement = '1';
        $lastElement = '2';
        $col = new \Gbili\Stdlib\CircularCollection(array($firstElement, $lastElement));
        $firstLap = $col->getLap();
        $col->nextLap();
        $this->assertEquals(++$firstLap, $col->getLap());
    }

    public function testGetNextReturnsFirstElementAfterCallToNextLap()
    {
        $firstElement = '1';
        $lastElement = '2';
        $col = new \Gbili\Stdlib\CircularCollection(array($firstElement, $lastElement));
        $firstLap = $col->getLap();
        $col->nextLap();
        $this->assertEquals($firstElement, $col->getNext());
    }

    public function testConsecutiveCallsToGetNextReturnEveryElementEvenAfterLapHasChanged()
    {
        $col = new \Gbili\Stdlib\CircularCollection(array('1', '2', '3', '4'));
        $firstLap = $col->getLap();

        $this->assertEquals('1', $col->getNext());
        $this->assertEquals('2', $col->getNext());
        $this->assertEquals('3', $col->getNext());
        $this->assertEquals('4', $col->getNext());
                                 
        $this->assertEquals('1', $col->getNext());
        $this->assertEquals(++$firstLap, $col->getLap());
        $this->assertEquals('2', $col->getNext());
        $this->assertEquals('3', $col->getNext());
        $this->assertEquals('4', $col->getNext());
    }

    public function testLastCallToGetNextChangedLapIsTrueOnlyWhenSubsequentCallsToGetNextHaveReturnedLastElementAndFirstElement()
    {
        $firstElement = '1';
        $lastElement = '2';
        $col = new \Gbili\Stdlib\CircularCollection(array($firstElement, $lastElement));

        $lap = 0;

        $this->assertEquals($lap, $col->getLap());
        $this->assertEquals(false, $col->lastCallToGetNextChangedLap());
        $this->assertEquals($firstElement, $col->getNext());
        $this->assertEquals(false, $col->lastCallToGetNextChangedLap());
        $this->assertEquals($lastElement, $col->getNext());
        $this->assertEquals(false, $col->lastCallToGetNextChangedLap());

        for (++$lap;$lap < 5; ++$lap) {
            $this->assertEquals($firstElement, $col->getNext());
            $this->assertEquals($lap, $col->getLap());
            $this->assertEquals(true, $col->lastCallToGetNextChangedLap());
            $this->assertEquals($lastElement, $col->getNext());
            $this->assertEquals(false, $col->lastCallToGetNextChangedLap());
        } 
    }
}
