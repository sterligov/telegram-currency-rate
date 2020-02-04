<?php


namespace App\Currency;


use App\Container;
use App\Exception\CurrencyConverterException;
use Longman\TelegramBot\TelegramLog;
use Psr\Cache\CacheItemPoolInterface;

class PipelineConverter implements ConverterInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    private CacheItemPoolInterface $cache;

    /**
     * @var ConverterInterface[]
     */
    private array $pipeline = [];

    /**
     * PipelineConverter constructor.
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
        $this->pipeline = [
            Container::get(RussianCentralBank::class),
            Container::get(EuropeanCentralBank::class)
        ];
    }

    /**
     * @param ConverterInterface[] $pipeline
     */
    public function setPipeline(array $pipeline)
    {
        $this->pipeline = $pipeline;
    }

    /**
     * @param $from
     * @param $to
     * @param int $amount
     * @return float|int|mixed
     * @throws CurrencyConverterException
     */
    public function convert($from, $to, $amount = 1)
    {
        $rate = 0;
        foreach ($this->pipeline as $converter) {
            try {
                $rate = $converter->convert($from, $to);
                break;
            } catch (\Exception $e) {
                TelegramLog::debug($e->getMessage());
            }
        }

        if (!$rate) {
            throw new CurrencyConverterException('Cannot get result from external currency api');
        }

        return $amount * $rate;
    }
}