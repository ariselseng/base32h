<?php

namespace Ariselseng\Base32H\Tests;


use Ariselseng\Base32H\Base32H;
use PHPUnit\Framework\TestCase;

class Base32HTest extends TestCase
{

    public function testEncode()
    {
        $this->assertEquals('L', Base32H::Encode(20));
        $this->assertEquals('1', Base32H::Encode(1));
        $this->assertEquals('3V', Base32H::Encode(123));
        $this->assertEquals('14RC0NJ', Base32H::Encode(1234567890));
        $this->assertEquals('132TLY9FTL5', Base32H::Encode(1234567890987653));
    }

    public function testDecode()
    {
        $this->assertEquals(20, Base32H::Decode('L'));
        $this->assertEquals(1, Base32H::Decode('I'));
        $this->assertEquals(31, Base32H::Decode('Z'));
        $this->assertEquals(123, Base32H::Decode('3V'));
        $this->assertEquals(1234567890, Base32H::Decode('14RC0NJ'));
        $this->assertEquals(1234567890987653, Base32H::Decode('132TLY9FTL5'));
    }
}
