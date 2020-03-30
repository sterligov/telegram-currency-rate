<?php


namespace App\Tests;


use App\Currency\Currency;
use App\Currency\PipelineConverter;
use App\Currency\ConverterInterface;
use App\Exception\CurrencyConverterException;
use PHPUnit\Framework\TestCase;

class PipelineConverterTest extends TestCase
{
    public function testConverter()
    {
        $rate = 2;
        $exception = new CurrencyConverterException();

        $converter1 = $this->createMock(ConverterInterface::class);
        $converter1->method('convert')
            ->willThrowException($exception);

        $converter2 = $this->createMock(ConverterInterface::class);
        $converter2->method('convert')
            ->willReturn($rate);

        $pipeline = new PipelineConverter([
            $converter1,
            $converter2
        ]);

        $this->assertEquals($rate, $pipeline->convert(new Currency(978), new Currency(643)));
    }

    public function testConverterException()
    {
        $exception = new CurrencyConverterException();

        $converter = $this->createMock(ConverterInterface::class);
        $converter->method('convert')
            ->willThrowException($exception);

        $pipeline = new PipelineConverter([$converter]);

        $this->expectException(CurrencyConverterException::class);
        $pipeline->convert(new Currency(978), new Currency(643));

    }
}