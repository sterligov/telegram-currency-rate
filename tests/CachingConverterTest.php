<?php


namespace App\Tests;

use App\Currency\CachingConverter;
use App\Currency\Currency;
use App\Currency\RussianCentralBank;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CachingConverterTest extends TestCase
{
    public function testConvertWithCache()
    {
        $expectedRate = 2;
        $cacheMock = $this->cacheMock(true, $expectedRate);
        $converterMock = $this->createMock(RussianCentralBank::class);
        $converter = new CachingConverter($converterMock, $cacheMock);
        $rate = $converter->convert(new Currency(643), new Currency(978));

        $this->assertEquals($expectedRate, $rate);
    }

    private function cacheMock($isHit, $rate)
    {
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->method('isHit')
            ->willReturn($isHit);
        $cacheItem->method('get')
            ->willReturn($rate);

        $cache = $this->createMock(CacheItemPoolInterface::class);
        $cache->method('getItem')
            ->willReturn($cacheItem);

        return $cache;
    }
}
