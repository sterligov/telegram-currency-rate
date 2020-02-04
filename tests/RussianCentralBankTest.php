<?php

namespace App\Tests;


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
     */
    public function testConvert($from, $to, $expected)
    {
        $this->assertEquals($expected, $this->converter->convert($from, $to));
    }

    public function testConvertExceptions()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->converter->convert(-1, 10);
    }
}