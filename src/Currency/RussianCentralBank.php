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
     * @param Currency|null $from
     * @param Currency|null $to
     * @param float $amount
     * @return float|int|mixed
     * @throws \App\Exception\CurrencyConverterException
     */
    public function convert(?Currency $from, ?Currency $to, float $amount = 1)
    {
        $fromCode = $from->convertCode(Currency::NUMBER);
        $toCode = $to->convertCode(Currency::NUMBER);

        $response = $this->client->get('http://www.cbr.ru/scripts/XML_daily_eng.asp');
        $xml = new \SimpleXMLElement($response->getBody()->getContents());

        $fromValue = $xml->xpath("//Valute[NumCode=$fromCode]")[0] ?? '';
        if ($fromValue) {
            $val = str_replace(',', '.', (string)$fromValue->Value);
            $fromValue = 1 / ($val / (int)$fromValue->Nominal);
        }

        $toValue = $xml->xpath("//Valute[NumCode=$toCode]")[0] ?? '';
        if ($toValue) {
            $val = str_replace(',', '.', (string)$toValue->Value);
            $toValue = 1 / ($val / (int)$toValue->Nominal);
        }

        return $amount * $this->convertWithBaseCurrency($fromCode, $toCode, self::BASE_CURRENCY, $fromValue, $toValue);
    }

    /**
     * @param Currency|null $from
     * @param Currency|null $to
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws \Exception
     */
    public function periodCurrencyRate(?Currency $from, ?Currency $to, string $startDate, string $endDate): array
    {
        $fromValues = [];
        $toValues = [];
        $fromCode = '';
        $toCode = '';
        $startDates = [];
        $endDates = [];

        if ($from) {
            $fromCode = $from->convertCode(Currency::RUSSIAN_BANK_ID);
            $url = "http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=$startDate&date_req2=$endDate&VAL_NM_RQ=$fromCode";
            $response = $this->client->get($url);
            [$startDates, $fromValues] = $this->parseCurrencyPeriodXML($response->getBody()->getContents());
        }

        if ($to) {
            $toCode = $to->convertCode(Currency::RUSSIAN_BANK_ID);
            $url = "http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=$startDate&date_req2=$endDate&VAL_NM_RQ=$toCode";
            $response = $this->client->get($url);
            [$endDates, $toValues] = $this->parseCurrencyPeriodXML($response->getBody()->getContents());
        }

        $values = [];
        $nValue = max(count($fromValues), count($toValues));

        for ($i = 0; $i < $nValue; $i++) {
            $values[] = $this->convertWithBaseCurrency(
                $fromCode,
                $toCode,
                'R00001',
                $fromValues[$i] ?? '',
                $toValues[$i] ?? ''
            );
        }

        return $this->fillEmptyDates($startDates ?: $endDates, $values);
    }

    /**
     * @param array $dates
     * @param array $values
     * @return array
     * @throws \Exception
     */
    private function fillEmptyDates(array $dates, array $values)
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
     * @param string $xmlString
     * @return array
     */
    private function parseCurrencyPeriodXML(string $xmlString)
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
