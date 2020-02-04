<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 10/23/19
 * Time: 11:10 PM
 */

namespace App\Tests;


use \PHPUnit\Framework\TestCase;
use \App\Currency\CodeConverter;

class CurrencyCodeConverterTest extends TestCase
{

    public function convertToAlphaData()
    {
        return [
            ['usd', 'USD'],
            [643, 'RUB'],
            ['European Union', 'EUR'],
            ['\ud83c\uddf7\ud83c\uddfa', 'RUB']
        ];
    }

    public function convertToNumData()
    {
        return [
            ['usd', 840],
            [643, 643],
            ['European Union', 978],
            ['\ud83c\uddfa\ud83c\uddf8', 840]
        ];
    }

    /**
     * @param $currency
     * @param $expected
     * @dataProvider convertToAlphaData
     */
    public function testConvertToAlpha($currency, $expected)
    {
        $this->assertEquals($expected, CodeConverter::toAlpha($currency));
    }

    /**
     * @param $currency
     * @param $expected
     * @dataProvider convertToNumData
     */
    public function testConvertToNum($currency, $expected)
    {
        $this->assertEquals($expected, CodeConverter::toNum($currency));
    }

    public function testConvertToNumException()
    {
        $this->expectException(\InvalidArgumentException::class);

        CodeConverter::toNum(-1);
    }

    public function testConvertToAplhaException()
    {
        $this->expectException(\InvalidArgumentException::class);

        CodeConverter::toNum('ff');
    }
}