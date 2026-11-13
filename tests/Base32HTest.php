<?php

declare(strict_types=1);

namespace Ariselseng\Base32H\Tests;

use Ariselseng\Base32H\Base32H;
use InvalidArgumentException;
use OverflowException;
use PHPUnit\Framework\TestCase;

class Base32HTest extends TestCase
{
    public function testEncode(): void
    {
        $this->assertSame('0', Base32H::Encode(0));
        $this->assertSame('L', Base32H::Encode(20));
        $this->assertSame('1', Base32H::Encode(1));
        $this->assertSame('3V', Base32H::Encode(123));
        $this->assertSame('14RC0NJ', Base32H::Encode(1234567890));
        $this->assertSame('132TLY9FTL5', Base32H::Encode(1234567890987653));
        $this->assertSame('7ZZZZZZZZZZZZ', Base32H::Encode(PHP_INT_MAX));
    }

    public function testEncodeRejectsNegativeNumbers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Base32H::Encode(-5);
    }

    public function testDecode(): void
    {
        $this->assertSame(0, Base32H::Decode('0'));
        $this->assertSame(0, Base32H::Decode(''));
        $this->assertSame(20, Base32H::Decode('L'));
        $this->assertSame(1, Base32H::Decode('I'));
        $this->assertSame(31, Base32H::Decode('Z'));
        $this->assertSame(123, Base32H::Decode('3V'));
        $this->assertSame(123, Base32H::Decode('3-v'));
        $this->assertSame(1234567890, Base32H::Decode('14RC0NJ'));
        $this->assertSame(1234567890987653, Base32H::Decode('132TLY9FTL5'));
        $this->assertSame(PHP_INT_MAX, Base32H::Decode('7ZZZZZZZZZZZZ'));
    }

    public function testDecodeRejectsValuesLargerThanAnInteger(): void
    {
        $this->expectException(OverflowException::class);
        Base32H::Decode('80000000000000');
    }

    public function testEncodeDecodeRoundTrip(): void
    {
        foreach ([0, 1, 31, 32, 2 ** 53 + 1, PHP_INT_MAX - 1, PHP_INT_MAX] as $number) {
            $this->assertSame($number, Base32H::Decode(Base32H::Encode($number)));
        }
    }

    public function testEncodeBin(): void
    {
        $this->assertSame('', Base32H::EncodeBin([]));
        $this->assertSame('', Base32H::EncodeBin(''));
        $this->assertSame('WELLH0WDYPARDNER', Base32H::EncodeBin([227, 169, 72, 131, 141, 245, 213, 150, 217, 217]));
        $this->assertSame('ZZZZZZZZ', Base32H::EncodeBin([255, 255, 255, 255, 255]));
        $this->assertSame('000FZZZZ', Base32H::EncodeBin([255, 255, 255]));
        $this->assertSame('0000007Z', Base32H::EncodeBin([255]));
        $this->assertSame('017L6N9M99M6YVAFF5CKGJKA6HNM0YAR', Base32H::EncodeBin('OCY5JjomOyY8Jj4mPyY'));
    }

    public function testEncodeBinRejectsValuesOutsideByteRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Base32H::EncodeBin([256]);
    }

    public function testEncodeBinRejectsNegativeValues(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Base32H::EncodeBin([-1]);
    }

    public function testDecodeBin(): void
    {
        $this->assertSame([], Base32H::DecodeBin(''));
        $this->assertSame([255, 255, 255, 255, 255], Base32H::DecodeBin('ZZZZZZZZ'));
        $this->assertSame([0, 0, 0, 0, 255], Base32H::DecodeBin('0000007Z'));
        $this->assertSame([0, 0, 0, 0, 255], Base32H::DecodeBin('7Z'));
        $this->assertSame([0, 0, 0, 0, 255], Base32H::DecodeBin('0000-007z'));
        $this->assertSame([227, 169, 72, 131, 141, 245, 213, 150, 217, 217], Base32H::DecodeBin('WELL-H0WD-YPAR-DNER'));
        $this->assertSame(
            [0, 0, 0, 0, 255, 255, 255, 255, 255, 255],
            Base32H::DecodeBin('7ZZZZZZZZZ')
        );
        $this->assertSame(
            [0, 79, 67, 89, 53, 74, 106, 111, 109, 79, 121, 89, 56, 74, 106, 52, 109, 80, 121, 89],
            Base32H::DecodeBin('017L6N9M99M6YVAFF5CKGJKA6HNM0YAR')
        );
    }

    public function testBinaryRoundTrip(): void
    {
        $bytes = [0, 1, 2, 3, 127, 128, 200, 254, 255, 42, 7];
        $this->assertSame(
            array_merge([0, 0, 0, 0], $bytes),
            Base32H::DecodeBin(Base32H::EncodeBin($bytes))
        );
    }
}
