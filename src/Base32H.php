<?php

namespace Ariselseng\Base32H;


class Base32H
{

    const DIGITS = [
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

    /**
     * @param int $input
     * @return string
     */
    public static function Encode(int $input)
    {
        $out = [];
        while ($input) {
            array_unshift($out, self::DIGITS[$input % 32][0]);
            $input = (int)($input / 32);
        }
        return join('', $out);
    }

    /**
     * @param int[]|string $input
     * @return string
     */
    public static function EncodeBin($input)
    {
        if (is_string($input)) {
            $input = array_map(function ($character) {
                return ord($character);
            }, str_split($input, 1));
        }

        $encodedFull = '';

        $overflow = count($input) % 5;
        if ($overflow) {
            $neededSize = (count($input) + (5 - $overflow));
            $input = array_pad($input, $neededSize * -1, 0);
        }

        for ($i = 0; $i < count($input); $i += 5) {
            $byteSegment = array_slice($input, $i, $i + 5);
            $IntSegment =
                $byteSegment[0] * 2 ** 32 +
                $byteSegment[1] * 2 ** 24 +
                $byteSegment[2] * 2 ** 16 +
                $byteSegment[3] * 2 ** 8 +
                $byteSegment[4];

            $encodedSegment = self::Encode($IntSegment);
            $encodedFull .= str_pad($encodedSegment, 8, '0', STR_PAD_LEFT);
        }

        return $encodedFull;
    }

    /**
     * @param string $character
     * @return int
     */
    private static function decodeCharacter(string $character)
    {
        foreach (self::DIGITS as $key => $digit) {
            if (strpos($digit, $character) !== false) {
                return $key;
            }
        }

        return -1;
    }

    /**
     * @param string $input
     * @return int
     */
    public static function Decode(string $input)
    {
        $decoded = 0;
        $count = 0;
        $charactersArray = array_reverse(str_split($input, 1));

        foreach ($charactersArray as $character) {
            $digit = self::decodeCharacter($character);

            if ($digit === -1) {
                continue;
            }

            $decoded += $digit * 32 ** $count;
            $count += 1;
        }

        return $decoded;
    }

    /**
     * @param string $input
     * @return int[]
     */
    public static function DecodeBin(string $input)
    {
        $input = str_pad($input, 8, '0', STR_PAD_LEFT);
        $byteArray = [];
        for ($i = 0; $i < strlen($input); $i += 8) {
            $decoded = self::Decode(substr($input, $i, 8));
            $paddedVal = str_pad(base_convert($decoded, 10, 16), 10, '0', STR_PAD_LEFT);
            $byteArray[] = (int)base_convert(substr($paddedVal, 0, 2), 16, 10);
            $byteArray[] = (int)base_convert(substr($paddedVal, 2, 2), 16, 10);
            $byteArray[] = (int)base_convert(substr($paddedVal, 4, 2), 16, 10);
            $byteArray[] = (int)base_convert(substr($paddedVal, 6, 2), 16, 10);
            $byteArray[] = (int)base_convert(substr($paddedVal, 8, 2), 16, 10);
        }

        return $byteArray;
    }
}