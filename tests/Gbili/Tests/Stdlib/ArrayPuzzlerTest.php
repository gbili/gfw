<?php
namespace Gbili\Tests\Stdlib;

class ArrayPuzzlerTest extends \Gbili\Tests\GbiliTestCase
{
    public function testReturnsKeysOfKeysAndValuesOfValuesByIntersectingValuesOfKeysWithKeysOfValues()
    {
        $keys = ['0k' => 'zero', '1k' => 'one', '2k' => 'two'];
        $values = ['zero' => '0v', 'one' => '1v', 'two' => '2v'];

        $puzzler = new \Gbili\Stdlib\ArrayPuzzler;
        $puzzle = $puzzler->puzzle($keys, $values);

        $ok = null;
        // Definition: intersectable_ref is $keys->value or $values->key
        foreach ($puzzle as $puzzledKey => $puzzledValue) {
            $intersectableRefInKeys = $keys[$puzzledKey];
            $valueMapedByIntersectableRefInValues = $values[$intersectableRefInKeys];
            if ($valueMapedByIntersectableRefInValues !== $puzzledValue) {
                $ok = false;
                break;
            } 
            $ok = true;
        }

        $this->assertEquals(true, $ok);
    }

    public function testReturnsSameAmounOfElementsInKeysWhenThereIsAFullIntersectionBetweenKeysAndValues()
    {
        $keys = ['0k' => 'zero', '1k' => 'one', '2k' => 'two'];
        $values = ['zero' => '0v', 'one' => '1v', 'two' => '2v'];

        $puzzler = new \Gbili\Stdlib\ArrayPuzzler;
        $puzzle = $puzzler->puzzle($keys, $values);

        $this->assertEquals(count($keys), count($puzzle));
    }

    public function testReturnsSameAmounOfElementsInValuesWhenThereIsAFullIntersectionBetweenKeysAndValues()
    {
        $keys = ['0k' => 'zero', '1k' => 'one', '2k' => 'two'];
        $values = ['zero' => '0v', 'one' => '1v', 'two' => '2v'];

        $puzzler = new \Gbili\Stdlib\ArrayPuzzler;
        $puzzle = $puzzler->puzzle($keys, $values);

        $this->assertEquals(count($values), count($puzzle));
    }

    public function testReturnsTheMostAmountOfElementsPuzzleWhenThereAreMissingKeys()
    {
        $keys = ['0k' => 'zero', '2k' => 'two']; //removed one element
        $values = ['zero' => '0v', 'one' => '1v', 'two' => '2v'];

        $puzzler = new \Gbili\Stdlib\ArrayPuzzler;
        $puzzle = $puzzler->puzzle($keys, $values);

        $this->assertEquals(count($keys), count($puzzle));
    }

    public function testReturnsTheMostAmountOfElementsPuzzleWhenThereAreMissingValues()
    {
        $keys = ['0k' => 'zero', '1k' => 'one', '2k' => 'two'];
        $values = ['zero' => '0v', 'two' => '2v']; //removed one element

        $puzzler = new \Gbili\Stdlib\ArrayPuzzler;
        $puzzle = $puzzler->puzzle($keys, $values);

        $this->assertEquals(count($values), count($puzzle));
    }

    public function testReturnsArrayWhenNoIntersectionIsFound()
    {
        $keys = ['a', 'b'];
        $values = ['c', 'd'];

        $puzzler = new \Gbili\Stdlib\ArrayPuzzler;
        $puzzle = $puzzler->puzzle($keys, $values);

        $this->assertEquals(true, is_array($puzzle));
    }

    public function testReturnsEmptyArrayWhenNoIntersectionIsFound()
    {
        $keys = ['a', 'b'];
        $values = ['c', 'd'];

        $puzzler = new \Gbili\Stdlib\ArrayPuzzler;
        $puzzle = $puzzler->puzzle($keys, $values);

        $this->assertEquals(true, empty($puzzle));
    }
}
