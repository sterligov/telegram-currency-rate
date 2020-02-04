<?php


namespace App\Chart;


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
     * @param $fromCurrency
     * @param $toCurrency
     * @param array $dates
     * @return array|\SVG\SVG
     * @throws \App\Exception\CoordinatePlaneException
     */
    public function draw($fromCurrency, $toCurrency, array $dates)
    {
        [, $values] = $this->currencyConverter->periodCurrencyRate(
            $fromCurrency,
            $toCurrency,
            $dates[0],
            $dates[count($dates) - 1]
        );

        return $this->chart->draw($dates, $values);
    }
}