<?php
namespace Gbili\Tests\Stdlib;

class SwappableKeysTest extends \Gbili\Tests\GbiliTestCase
{
    /**
     * Sets up the fixture, for exaple, open a network connection
     * This method is called before a test is executed
     *
     * @return void
     */
    public function setUp()
    {
        $this->myArray = ['one' => ['two' => ['three' => ['four' => 'value']]]];
    }

    public function testReturnsValueWhenKeysArePassedInOrder()
    {
        $swappableKeys = new \Gbili\Stdlib\SwappableKeys;
        $this->assertEquals('value', $swappableKeys->get(['one', 'two', 'three', 'four'], $this->myArray, 'not_found'));
    }

    public function testReturnsValueWhenKeysArePassedUnordered()
    {
        $swappableKeys = new \Gbili\Stdlib\SwappableKeys;
        $this->assertEquals('value', $swappableKeys->get(['three', 'two', 'one', 'four'], $this->myArray, 'not_found'));
    }

    public function testReturnsValueEvenWhenThirdParamsIsFalse()
    {
        $swappableKeys = new \Gbili\Stdlib\SwappableKeys;
        $this->assertEquals('value', $swappableKeys->get(['three', 'two', 'one', 'four'], $this->myArray, false));
    }

    public function testReturnsNotFoundWhenFirstKeyIsWrong()
    {
        $swappableKeys = new \Gbili\Stdlib\SwappableKeys;
        $this->assertEquals('not_found', $swappableKeys->get(['wrong', 'two', 'three', 'four'], $this->myArray, 'not_found'));
    }

    public function testReturnsNotFoundWhenKeyInTheMiddleIsWrong()
    {
        $swappableKeys = new \Gbili\Stdlib\SwappableKeys;
        $this->assertEquals('not_found', $swappableKeys->get(['one', 'three', 'wrong', 'four'], $this->myArray, 'not_found'));
    }

    public function testReturnsNotFoundWhenLastKeyWrong()
    {
        $swappableKeys = new \Gbili\Stdlib\SwappableKeys;
        $this->assertEquals('not_found', $swappableKeys->get(['one', 'three', 'two', 'wrong'], $this->myArray, 'not_found'));
    }

    public function testReturnsNotFoundWhenTooManyKeys()
    {
        $swappableKeys = new \Gbili\Stdlib\SwappableKeys;
        $this->assertEquals('not_found', $swappableKeys->get(['one', 'one', 'three', 'two', 'wrong'], $this->myArray, 'not_found'));
    }

    public function testReturnsNotFoundWhenTooManyKeysAllOkExceptOne()
    {
        $swappableKeys = new \Gbili\Stdlib\SwappableKeys;
        $this->assertEquals('not_found', $swappableKeys->get(['one', 'four', 'three', 'two', 'wrong'], $this->myArray, 'not_found'));
    }

    public function testReturnsValueEvenWhenValueIsAnArray()
    {
        $myArraysValueIsAnArray = ['one' => ['two' => ['three' => ['four' => []]]]];
        $swappableKeys = new \Gbili\Stdlib\SwappableKeys;
        $this->assertEquals([], $swappableKeys->get(['three', 'two', 'one', 'four'], $myArraysValueIsAnArray, false));
    }
}
