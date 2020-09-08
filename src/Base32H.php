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
}