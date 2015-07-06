<?php

namespace Krak\Tests;

use Datetime,
    Krak\Marshal as m;

class UtilTest extends TestCase
{
    public function testMarshalMarshaler()
    {
        $this->assertNull(m\marshal(new StubMarshaler(), ''));
    }
    public function testMarshalClosure()
    {
        $this->assertNull(m\marshal(function($val){}, ''));
    }
    public function testMarshalCallable()
    {
        $this->assertNull(m\marshal([new StubMarshaler(), 'stubMarshal'], ''));
    }

    public function testMarshalException()
    {
        try {
            m\marshal(null, '');
            $this->assertTrue(false);
        } catch (M\InvalidMarshalerException $e) {
            $this->assertTrue(true);
        }
    }

    public function testTimestampNull()
    {
        $this->assertNull(m\timestamp(''));
    }
    public function testTimestamp()
    {
        $this->assertInternalType('int', m\timestamp(new DateTime()));
    }
}

class StubMarshaler implements M\Marshaler
{
    public function marshal($value)
    {
        return null;
    }

    public function stubMarshal($value)
    {
        return null;
    }
}
