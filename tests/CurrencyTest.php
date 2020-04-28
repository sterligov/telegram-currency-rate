<?php


namespace App\Tests;

use App\Currency\Currency;
use App\Exception\CurrencyConverterException;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    public function codes()
    {
        return [
            [643, 'alpha', 'RUB'],
            ['RUB', 'num', 643],
            ['USA', 'num', 840],
            ['\ud83c\uddfa\ud83c\uddf8', 'alpha', 'USD'],
            ['European Union', 'custom_id', 'R01239']
        ];
    }

    /**
     * @dataProvider codes
     */
    public function testConvertCode($initialCode, $type, $finiteCode)
    {
        $currency = new Currency($initialCode);

        $this->assertEquals($finiteCode, $currency->convertCode($type));
    }

    public function testInvalidFromCode()
    {
        $currency = new Currency('bad_code');
        $this->expectException(CurrencyConverterException::class);

        $currency->convertCode(Currency::ALPHA);
    }

    public function testInvalidToCode()
    {
        $currency = new Currency(978);
        $this->expectException(CurrencyConverterException::class);

        $currency->convertCode('bad_Code');
    }
}
