<?php

namespace Krak\Tests;

use Krak\Marshal as m;

class MarshalersTest extends TestCase
{
    public function testMock()
    {
        $m = m\mock('abc');
        $this->assertEquals('abc', $m(null));
    }

    public function testMap()
    {
        $m = m\map(m\mock(2));
        $this->assertEquals([2], $m([1]));
    }

    public function testPipe()
    {
        $add = function($val) {
            return $val + 1;
        };
        $m = m\pipe([$add, $add]);
        $this->assertEquals(2, $m(0));
    }

    public function testMerge()
    {
        $m = m\merge([
            m\mock(['a' => 1]),
            m\mock(['b' => 2])
        ]);
        $this->assertEquals(
            ['a' => 1, 'b' => 2],
            $m(null)
        );
    }

    public function testKeys()
    {
        $m = m\keys(['id']);
        $this->assertEquals(
            ['id' => 1],
            $m(['id' => 1, 'a' => 2])
        );
    }

    public function testProperties()
    {
        $m = m\properties(['id']);
        $this->assertEquals(
            ['id' => 1],
            $m((object) ['id' => 1, 'a' => 2])
        );
    }

    public function testFields()
    {
        $m = m\fields(m\key_accessor(), ['id']);
        $this->assertEquals(
            ['id' => 1],
            $m(['id' => 1, 'a' => 2])
        );
    }

    public function testCollection()
    {
        $m = m\collection(m\key_accessor(), [
            'a' => m\mock(1),
            'b' => m\mock(2),
        ]);

        $this->assertEquals(
            ['a' => 1, 'b' => 2],
            $m(['a' => null, 'b' => null])
        );
    }

    public function testId()
    {
        $m = m\id();
        $this->assertEquals(1, $m(1));
    }
    public function testIdentity()
    {
        $m = m\identity();
        $this->assertEquals(1, $m(1));
    }

    public function testNotNull()
    {
        $m = m\notnull(m\mock(1));
        return $this->assertEquals(1, $m(1));
    }

    public function testNotNullWithNull()
    {
        $m = m\notnull(m\mock(1));
        return $this->assertNull($m(null));
    }
}
