<?php


namespace App\Currency;

use Longman\TelegramBot\TelegramLog;
use Psr\Cache\CacheItemPoolInterface;

class CachingConverter implements ConverterInterface
{
    /**
     * @var ConverterInterface
     */
    private ConverterInterface $converter;

    /**
     * @var CacheItemPoolInterface
     */
    private CacheItemPoolInterface $cache;

    /**
     * CachingConverter constructor.
     * @param ConverterInterface $converter
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(ConverterInterface $converter, CacheItemPoolInterface $cache)
    {
        $this->converter = $converter;
        $this->cache = $cache;
    }

    /**
     * @param Currency $from
     * @param Currency $to
     * @param float $amount
     * @return mixed
     * @throws \App\Exception\CurrencyConverterException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function convert(Currency $from, Currency $to, float $amount = 1.0)
    {
        $key = 'currency.' . $from->convertCode(Currency::ALPHA) . '.' . $to->convertCode(Currency::ALPHA);
        $rateCache = $this->cache->getItem($key);

        if (!$rateCache->isHit()) {
            try {
                $rate = $this->converter->convert($from, $to);
                $expireDate = (new \DateTime())
                    ->modify('+1 day')
                    ->setTime(0, 0, 0);
                $rateCache->expiresAt($expireDate);
                $rateCache->set($rate);
                $this->cache->save($rateCache);
            } catch (\Exception $e) {
                TelegramLog::debug($e->getMessage());
            }
        }

        return $rateCache->get();
    }
}
