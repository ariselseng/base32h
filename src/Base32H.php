<?php

declare(strict_types=1);

namespace Ariselseng\Base32H;

use InvalidArgumentException;
use OverflowException;

class Base32H
{
    public const DIGITS = [
        '0Oo',
        '1Ii',
        '2',
        '3',
        '4',
        '5Ss',
        '6',
        '7',
        '8',
        '9',
        'Aa',
        'Bb',
        'Cc',
        'Dd',
        'Ee',
        'Ff',
        'Gg',
        'Hh',
        'Jj',
        'Kk',
        'Ll',
        'Mm',
        'Nn',
        'Pp',
        'Qq',
        'Rr',
        'Tt',
        'VvUu',
        'Ww',
        'Xx',
        'Yy',
        'Zz',
    ];

    /** @var array<string, int>|null */
    private static ?array $digitValues = null;

    public static function Encode(int $input): string
    {
        if ($input < 0) {
            throw new InvalidArgumentException('Base32H cannot encode negative numbers');
        }

        if ($input === 0) {
            return self::DIGITS[0][0];
        }

        $out = '';
        while ($input > 0) {
            $out = self::DIGITS[$input % 32][0] . $out;
            $input = intdiv($input, 32);
        }

        return $out;
    }

    /**
     * @param int[]|string $input bytes as integers 0-255, or a binary string
     */
    public static function EncodeBin(array|string $input): string
    {
        if (is_string($input)) {
            $input = $input === '' ? [] : array_values(unpack('C*', $input));
        }

        foreach ($input as $byte) {
            if (!is_int($byte) || $byte < 0 || $byte > 255) {
                throw new InvalidArgumentException('Base32H::EncodeBin expects bytes in the range 0-255');
            }
        }

        $overflow = count($input) % 5;
        if ($overflow) {
            $input = array_merge(array_fill(0, 5 - $overflow, 0), array_values($input));
        }

        $encoded = '';
        foreach (array_chunk($input, 5) as $segment) {
            $value = ($segment[0] << 32)
                | ($segment[1] << 24)
                | ($segment[2] << 16)
                | ($segment[3] << 8)
                | $segment[4];

            $encoded .= str_pad(self::Encode($value), 8, '0', STR_PAD_LEFT);
        }

        return $encoded;
    }

    /**
     * Characters that are not Base32H digits (e.g. separators) are ignored.
     */
    public static function Decode(string $input): int
    {
        $decoded = 0;
        foreach (str_split(self::clean($input)) as $character) {
            $digit = self::digitValues()[$character];

            if ($decoded > intdiv(PHP_INT_MAX - $digit, 32)) {
                throw new OverflowException('Base32H value does not fit in an integer');
            }

            $decoded = $decoded * 32 + $digit;
        }

        return $decoded;
    }

    /**
     * Characters that are not Base32H digits (e.g. separators) are ignored.
     *
     * @return int[]
     */
    public static function DecodeBin(string $input): array
    {
        $input = self::clean($input);
        if ($input === '') {
            return [];
        }

        $overflow = strlen($input) % 8;
        if ($overflow) {
            $input = str_repeat('0', 8 - $overflow) . $input;
        }

        $bytes = [];
        foreach (str_split($input, 8) as $segment) {
            $value = self::Decode($segment);
            for ($shift = 32; $shift >= 0; $shift -= 8) {
                $bytes[] = ($value >> $shift) & 0xFF;
            }
        }

        return $bytes;
    }

    /**
     * Removes every character that is not a Base32H digit.
     */
    private static function clean(string $input): string
    {
        $values = self::digitValues();

        return implode('', array_filter(
            $input === '' ? [] : str_split($input),
            static fn (string $character): bool => isset($values[$character])
        ));
    }

    /**
     * @return array<string, int>
     */
    private static function digitValues(): array
    {
        if (self::$digitValues === null) {
            self::$digitValues = [];
            foreach (self::DIGITS as $value => $characters) {
                foreach (str_split($characters) as $character) {
                    self::$digitValues[$character] = $value;
                }
            }
        }

        return self::$digitValues;
    }
}
