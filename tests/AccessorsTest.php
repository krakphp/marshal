<?php

namespace Krak\Tests;

use Krak\Marshal as m;

class AccessorsTest extends TestCase
{
    public function testKeyAccessor()
    {
        list($get, $has) = m\key_accessor();
        $data = ['id' => 1];
        $valid = $get($data, 'id') == 1 && $has($data, 'id');
        $this->assertTrue($valid);
    }

    public function testPropertyAccessor()
    {
        list($get, $has) = m\property_accessor();
        $data = (object) ['id' => 1];
        $valid = $get($data, 'id') == 1 && $has($data, 'id');
        $this->assertTrue($valid);
    }
}
