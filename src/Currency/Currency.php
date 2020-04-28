<?php


namespace App\Currency;

use App\Exception\CurrencyConverterException;

class Currency
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

    const ALPHA = 'alpha';

    const NUMBER = 'num';

    const RUSSIAN_BANK_ID = 'custom_id';

    /**
     * @var mixed
     */
    private $currency;

    /**
     * Currency constructor.
     * @param $currency
     */
    public function __construct($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @param $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param $to
     * @return mixed
     * @throws CurrencyConverterException
     */
    public function convertCode($to)
    {
        $from = $this->currency;
        if (ctype_alpha($from) && in_array(strlen($from), [2, 3])) {
            $from = strtoupper($from);
        }

        $formats = array_keys(current(self::CURRENCY));
        foreach ($formats as $format) {
            $index = array_search($from, array_column(self::CURRENCY, $format));
            if ($index !== false) {
                $codes = array_values(self::CURRENCY)[$index];
                if (!isset($codes[$to])) {
                    throw new CurrencyConverterException("Invalid code $to");
                }

                return $codes[$to];
            }
        }

        throw new CurrencyConverterException("Invalid code $from");
    }
}
