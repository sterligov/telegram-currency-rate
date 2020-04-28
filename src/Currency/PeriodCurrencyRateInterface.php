<?php


namespace App\Currency;

interface PeriodCurrencyRateInterface
{
    /**
     * @param Currency|null $fromCurrency
     * @param Currency|null $toCurrency
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function periodCurrencyRate(?Currency $fromCurrency, ?Currency $toCurrency, string $startDate, string $endDate): array;
}
