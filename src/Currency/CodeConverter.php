<?php

namespace App\Currency;


class CodeConverter
{
    const CURRENCY = [
        'RUB' => [
            'num' => 643,
            'alpha' => 'RUB',
            'flag' => '\ud83c\uddf7\ud83c\uddfa',
            'name' => 'Russia',
            'custom_id' => 'R00001'
        ],
        'USD' => [
            'num' => 840,
            'alpha' => 'USD',
            'flag' => '\ud83c\uddfa\ud83c\uddf8',
            'name' => 'USA',
            'custom_id' => 'R01235'
        ],
        'EUR' => [
            'num' => 978,
            'alpha' => 'EUR',
            'flag' => '\ud83c\uddea\ud83c\uddfa',
            'name' => 'European Union',
            'custom_id' => 'R01239'
        ],
        'KZT' => [
            'num' => 398,
            'alpha' => 'KZT',
            'flag' => '\ud83c\uddf0\ud83c\uddff',
            'name' => 'Kazakhstan',
            'custom_id' => 'R01335'
        ],
        'UZS' => [
            'num' => 860,
            'alpha' => 'UZS',
            'flag' => '\ud83c\uddfa\ud83c\uddff',
            'name' => 'Uzbekistan',
            'custom_id' => 'R01717'
        ],
        'GBP' => [
            'num' => 826,
            'alpha' => 'GBP',
            'flag' => '\ud83c\uddec\ud83c\udde7',
            'name' => 'Britain',
            'custom_id' => 'R01035'
        ],
        'UAH' => [
            'num' => 980,
            'alpha' => 'UAH',
            'flag' => '\ud83c\uddfa\ud83c\udde6',
            'name' => 'Ukraine',
            'custom_id' => 'R01720'
        ],
        'BYN' => [
            'num' => 933,
            'alpha' => 'BYN',
            'flag' => '\ud83c\udde7\ud83c\uddfe',
            'name' => 'Belarus',
            'custom_id' => 'R01090B'
        ],
    ];

    /**
     * @param $currency
     * @return mixed
     */
    public static function toAlpha($currency)
    {
        return self::convert($currency, 'alpha');
    }

    /**
     * @param $currency
     * @return mixed
     */
    public static function toNum($currency)
    {
        return self::convert($currency, 'num');
    }

    /**
     * @param $from
     * @param $to
     * @return string
     */
    public static function convert($from, $to)
    {
        if (ctype_alpha($from) && in_array(strlen($from), [2, 3])) {
            $from = strtoupper($from);
        }

        $formats = array_keys(current(self::CURRENCY));

        foreach ($formats as $format) {
            $index = array_search($from, array_column(self::CURRENCY, $format));

            if ($index !== false) {
                return array_values(self::CURRENCY)[$index][$to];
            }
        }

        throw new \InvalidArgumentException("Invalid code $from");
    }
}