<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 10/22/19
 * Time: 8:15 PM
 */

namespace App\Currency;


use DateTime;

class RussianCentralBank extends AbstractConverter implements PeriodCurrencyRateInterface
{
    const BASE_CURRENCY = 643;

    /**
     * @param $from
     * @param $to
     * @param $amount
     * @return float|int|mixed
     * @throws \InvalidArgumentException
     */
    public function convert($from, $to, $amount = 1)
    {
        $from = CodeConverter::toNum($from);
        $to = CodeConverter::toNum($to);

        $response = $this->client->get('http://www.cbr.ru/scripts/XML_daily_eng.asp');
        $xml = new \SimpleXMLElement($response->getBody()->getContents());

        $fromValue = $xml->xpath("//Valute[NumCode=$from]")[0] ?? '';

        if ($fromValue) {
            $val = str_replace(',', '.', (string)$fromValue->Value);
            $fromValue = 1 / ($val / (int)$fromValue->Nominal);
        }

        $toValue = $xml->xpath("//Valute[NumCode=$to]")[0] ?? '';

        if ($toValue) {
            $val = str_replace(',', '.', (string)$toValue->Value);
            $toValue = 1 / ($val / (int)$toValue->Nominal);
        }

        return $amount * $this->convertWithBaseCurrency($from, $to, self::BASE_CURRENCY, $fromValue, $toValue);
    }

    /**
     * @param $fromCurrency
     * @param $toCurrency
     * @param $startDate
     * @param $endDate
     * @return array
     * @throws \Exception
     */
    public function periodCurrencyRate($fromCurrency, $toCurrency, $startDate, $endDate): array
    {
        $fromCurrency = CodeConverter::convert($fromCurrency, 'custom_id');
        $toCurrency = CodeConverter::convert($toCurrency, 'custom_id');

        $fromValues = [];
        $toValues = [];

        if ($fromCurrency) {
            $url = "http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=$startDate&date_req2=$endDate&VAL_NM_RQ=$fromCurrency";
            $response = $this->client->get($url);
            list($startDates, $fromValues) = $this->parseCurrencyPeriodXML($response->getBody()->getContents());
        }

        if ($toCurrency) {
            $url = "http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=$startDate&date_req2=$endDate&VAL_NM_RQ=$toCurrency";
            $response = $this->client->get($url);
            list($endDates, $toValues) = $this->parseCurrencyPeriodXML($response->getBody()->getContents());
        }

        $values = [];
        $nValue = count($fromValues);

        for ($i = 0; $i < $nValue; $i++) {
            $values[] = $this->convertWithBaseCurrency(
                $fromCurrency,
                $toCurrency,
                'R00001',
                $fromValues[$i] ?? '',
                $toValues[$i] ?? ''
            );
        }

        return $this->fillEmptyDates($startDates ?? $endDates, $values);
    }

    /**
     * @param $dates
     * @param $values
     * @return array
     * @throws \Exception
     */
    private function fillEmptyDates($dates, $values)
    {
        $fullDates[] = $dates[0];
        $fullValues[] = $values[0];

        for ($i = 1; $i < count($dates); $i++) {
            $d = new \DateTime($dates[$i]);
            $last = new DateTime($fullDates[count($fullDates) - 1]);

            while ($last->diff($d)->days > 1) {
                $fullValues[] = $fullValues[count($fullValues) - 1];
                $fullDates[] = $last->modify("+1 day")
                    ->format('d.m.Y');
            }

            $fullValues[] = $values[$i];
            $fullDates[] = $dates[$i];
        }

        return [$fullDates, $fullValues];
    }

    /**
     * @param $xmlString
     * @return array
     */
    private function parseCurrencyPeriodXML($xmlString)
    {
        $xml = new \SimpleXMLElement($xmlString);
        $dates = [];
        $values = [];

        foreach ($xml as $item) {
            $dates[] = (string)$item->attributes()['Date'];
            $val = str_replace(',', '.', (string)$item->Value);
            $values[] = 1 / ($val / (int)$item->Nominal);
        }

        return [$dates, $values];
    }
}