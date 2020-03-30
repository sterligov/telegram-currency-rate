<?php

namespace App\Tests;


use App\Currency\Currency;
use App\Exception\CurrencyCommandException;
use App\Exception\CurrencyConverterException;
use \PHPUnit\Framework\TestCase;

class RussianCentralBankTest extends TestCase
{
    const XML_TEST_DATA = 'russian_bank.xml';

    protected $converter;

    protected function setUp(): void
    {
        $this->converter = new \App\Currency\RussianCentralBank($this->getHttpClientMock());
    }

    protected function getHttpClientMock()
    {
        $httpClient = $this->getMockBuilder(\GuzzleHttp\Client::class)
            ->disableOriginalConstructor()
            ->addMethods(['get', 'getBody', 'getContents'])
            ->getMock();

        $httpClient->expects($this->any())
            ->method('get')
            ->will($this->returnSelf());

        $httpClient->expects($this->any())
            ->method('getBody')
            ->will($this->returnSelf());

        $xml = file_get_contents(__DIR__  . '/' . static::XML_TEST_DATA);

        $httpClient->expects($this->any())
            ->method('getContents')
            ->willReturn($xml);

        return $httpClient;
    }

    public function currencyData()
    {
        return [
            [643, 840, 0.0025],
            [978, 643, 500],
            [978, 840, 1.25],
            [643, 398, 0.125],
            [398, 643, 8]
        ];
    }

    /**
     * @dataProvider currencyData
     * @param $from
     * @param $to
     * @param $expected
     * @throws \App\Exception\CurrencyConverterException
     */
    public function testConvert($from, $to, $expected)
    {
        $this->assertEquals($expected, $this->converter->convert(new Currency($from), new Currency($to)));
    }

    public function invalidData()
    {
        return [
            [-1, 840],
            ['USD', 1],
            [-1, 'BAD_CURRENCY'],
        ];
    }

    /**
     * @dataProvider invalidData
     * @throws CurrencyConverterException
     */
    public function testConvertExceptions($from, $to)
    {
        $this->expectException(CurrencyConverterException::class);

        $this->converter->convert(new Currency($from), new Currency($to));
    }
}