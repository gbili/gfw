<?php
namespace Gbili\Tests\Stdlib;

class ArrayNestedKeyTest extends \Gbili\Tests\GbiliTestCase
{
    public function testUnsetElementRemovesElementUnderKeysAndLastKey()
    {
        $a = [
            'a' => [
                'b' => [
                    'c' => 'v', //element to remove
                    'c2' => 'v2',
                ],
                'b2' => 'v2',
            ],
        ];
        $expected = [
            'a' => [
                'b' => [
                    //element removed here
                    'c2' => 'v2',
                ],
                'b2' => 'v2',
            ],
        ];
        $o = new \Gbili\Stdlib\ArrayNestedKey;
        $this->assertEquals($expected, $o->unsetEl(['a', 'b', 'c'], $a));
    }

    public function testCanSetElement()
    {
        $a = [
            'a' => [
                'b' => [
                    'c' => 'v',
                    'c2' => 'v2',
                ],
                'b2' => 'v2',
            ],
        ];
        $expected = [
            'a' => [
                'b' => [
                    'c' => 'v',
                    'c2' => 'v2',
                    'c3' => 'v3',
                ],
                'b2' => 'v2',
            ],
        ];
        $o = new \Gbili\Stdlib\ArrayNestedKey;
        $this->assertEquals($expected, $o->setEl(['a', 'b', 'c3'], $a, 'v3'));
    }
}
