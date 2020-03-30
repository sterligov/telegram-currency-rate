<?php


namespace App\Chart;


use App\Currency\Currency;
use App\Currency\PeriodCurrencyRateInterface;

class PeriodCurrencyChart
{
    /**
     * @var PeriodCurrencyRateInterface
     */
    private PeriodCurrencyRateInterface $currencyConverter;

    /**
     * @var DynamicChart
     */
    private DynamicChart $chart;

    /**
     * PeriodCurrencyChart constructor.
     * @param PeriodCurrencyRateInterface $currencyConverter
     * @param DynamicChart $chart
     */
    public function __construct(
        PeriodCurrencyRateInterface $currencyConverter,
        DynamicChart $chart
    ) {
        $this->currencyConverter = $currencyConverter;
        $this->chart = $chart;
    }

    /**
     * @param Currency|null $from
     * @param Currency|null $to
     * @param array $dates
     * @return array|\SVG\SVG
     * @throws \App\Exception\CoordinatePlaneException
     */
    public function draw(?Currency $from, ?Currency $to, array $dates)
    {
        [, $values] = $this->currencyConverter->periodCurrencyRate(
            $from,
            $to,
            $dates[0],
            $dates[count($dates) - 1]
        );

        return $this->chart->draw($dates, $values);
    }
}