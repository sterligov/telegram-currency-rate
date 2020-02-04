<?php


namespace App\Currency;


interface PeriodCurrencyRateInterface
{
    public function periodCurrencyRate($fromCurrency, $toCurrency, $startDate, $endDate): array;
}