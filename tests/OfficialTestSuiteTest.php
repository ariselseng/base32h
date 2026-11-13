<?php

declare(strict_types=1);

namespace Ariselseng\Base32H\Tests;

use Ariselseng\Base32H\Base32H;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Conformance with the official Base32H test suite (https://github.com/base32h/base32h-tests,
 * vendored in tests/base32h-tests) and the examples in https://base32h.github.io/specification.
 */
class OfficialTestSuiteTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    private static function rows(string $file): iterable
    {
        $handle = fopen(__DIR__ . '/base32h-tests/' . $file, 'r');
        fgetcsv($handle, escape: '');
        while (($row = fgetcsv($handle, escape: '')) !== false) {
            if ($row !== [null]) {
                yield "$file: $row[0]" => [$row[0], $row[1]];
            }
        }
        fclose($handle);
    }

    /**
     * @return int[]
     */
    private static function bytes(string $dotted): array
    {
        return array_map('intval', explode('.', $dotted));
    }

    public static function encodeProvider(): iterable
    {
        yield from self::rows('digit-encode.csv');
        yield from self::rows('numeric-encode.csv');
    }

    public static function decodeProvider(): iterable
    {
        yield from self::rows('digit-decode.csv');
        yield from self::rows('numeric-decode.csv');
    }

    public static function binaryEncodeProvider(): iterable
    {
        yield from self::rows('binary-encode.csv');
    }

    public static function binaryDecodeProvider(): iterable
    {
        yield from self::rows('binary-decode.csv');
    }

    #[DataProvider('encodeProvider')]
    public function testEncode(string $decimal, string $base32h): void
    {
        $this->assertSame($base32h, Base32H::Encode((int) $decimal));
    }

    #[DataProvider('decodeProvider')]
    public function testDecode(string $base32h, string $decimal): void
    {
        $this->assertSame((int) $decimal, Base32H::Decode($base32h));
    }

    #[DataProvider('binaryEncodeProvider')]
    public function testEncodeBin(string $bytes, string $base32h): void
    {
        $this->assertSame($base32h, Base32H::EncodeBin(self::bytes($bytes)));
    }

    #[DataProvider('binaryDecodeProvider')]
    public function testDecodeBin(string $base32h, string $bytes): void
    {
        $this->assertSame(self::bytes($bytes), Base32H::DecodeBin($base32h));
    }

    public function testSpecificationBinaryExamples(): void
    {
        $this->assertSame(
            'ZVNWMZMTPVFAVFPF04HLARW9MF6YZVEB',
            Base32H::EncodeBin(hex2bin('FEEDCAFEBABEDEADBEEF0123456789ABCDEFEDCB'))
        );
        $this->assertSame(
            array_values(unpack('C*', hex2bin('FEEDCAFEBABEDEADBEEF0123456789ABCDEFEDCB'))),
            Base32H::DecodeBin('ZVNW-MZMT PVFA-VFPF 04HL-ARW9 MF6Y-ZVEB')
        );

        $this->assertSame('000DXBDYXZ5FXEMY', Base32H::EncodeBin(hex2bin('DEADBEEFCAFEBABE')));
        $this->assertSame(
            array_values(unpack('C*', hex2bin('0000DEADBEEFCAFEBABE'))),
            Base32H::DecodeBin('D-XBDY XZ5F-XEMY')
        );
    }

    public function testSpecificationNumericRange(): void
    {
        $this->assertSame('ZZZZZZZZ', Base32H::Encode(1099511627775));
        $this->assertSame(1099511627775, Base32H::Decode('ZZZZ-ZZZZ'));
    }
}
