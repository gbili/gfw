<?php
namespace Gbili\Tests\Stdlib;

class SwappableKeysMergedTest extends \Gbili\Tests\GbiliTestCase
{
    /**
     * Sets up the fixture, for exaple, open a network connection
     * This method is called before a test is executed
     *
     * @return void
     */
    public function test()
    {
        $a = [
            'b' => [
                'c' => [
                    'a' => 'v', //first value for a-b-c
                    'c2' => 'v3',
                ],
                'b2' => 'v2',
            ],
            'a' => [
                'b' => [
                    'c' => 'v2',//second value for a-b-c
                ],
                'b2' => 'v3',
            ],
        ];
        $expected = [
            'v',
            'v2',
        ];
        $o = new \Gbili\Stdlib\SwappableKeysMerged;
        $found = $o->get(['a', 'b', 'c'], $a, 'nothing_found');
        $this->assertEquals($expected, $found);
    }
}
