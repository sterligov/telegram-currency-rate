<?php


namespace App\Currency;


use App\Container;
use App\Exception\CurrencyConverterException;
use Longman\TelegramBot\TelegramLog;
use Psr\Cache\CacheItemPoolInterface;

class PipelineConverter implements ConverterInterface
{
    /**
     * @var ConverterInterface[]
     */
    private array $pipeline = [];

    /**
     * PipelineConverter constructor.
     * @param array $pipeline
     */
    public function __construct(array $pipeline = [])
    {
        if (!$pipeline) {
            $pipeline = [
                Container::get(RussianCentralBank::class),
                Container::get(EuropeanCentralBank::class)
            ];
        }
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