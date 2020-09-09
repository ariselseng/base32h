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

    public function testEncodeBin()
    {
        $this->assertEquals('WELLH0WDYPARDNER', Base32H::EncodeBin([227, 169, 72, 131, 141, 245, 213, 150, 217, 217]));
        $this->assertEquals('ZZZZZZZZ', Base32H::EncodeBin([255, 255, 255, 255, 255]));
        $this->assertEquals('000FZZZZ', Base32H::EncodeBin([255, 255, 255]));
        $this->assertEquals('0000007Z', Base32H::EncodeBin([255]));
        $this->assertEquals('017L6N9M99M6YVAFF5CKGJKA6HNM0YAR', Base32H::EncodeBin('OCY5JjomOyY8Jj4mPyY'));
    }

    public function testDecodeBin()
    {
        $this->assertEquals([255, 255, 255, 255, 255], Base32H::DecodeBin('ZZZZZZZZ'));
        $this->assertEquals([0, 0, 0, 0, 255], Base32H::DecodeBin('0000007Z'));
        $this->assertEquals([0, 79, 67, 89, 53, 74, 106, 111, 109, 79, 121, 89, 56, 74, 106, 52, 109, 80, 121, 89], Base32H::DecodeBin('017L6N9M99M6YVAFF5CKGJKA6HNM0YAR'));
    }

}
