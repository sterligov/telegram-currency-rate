<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 10/22/19
 * Time: 8:17 PM
 */

namespace App\Currency;

class EuropeanCentralBank extends AbstractConverter
{
    const BASE_CURRENCY = 'EUR';

    /**
     * @param Currency|null $from
     * @param Currency|null $to
     * @param float $amount
     * @return float|int|mixed
     * @throws \App\Exception\CurrencyConverterException
     */
    public function convert(?Currency $from, ?Currency $to, float $amount = 1)
    {
        $fromCode = $from->convertCode(Currency::ALPHA);
        $toCode = $to->convertCode(Currency::ALPHA);

        $response = $this->client->get('https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml?34686d29f763ea6d2a8a22559b3df675');
        $xml = new \SimpleXMLElement($response->getBody()->getContents());
        $xml->registerXPathNamespace('c', 'http://www.ecb.int/vocabulary/2002-08-01/eurofxref');

        $fromValue = $xml->xpath("//c:Cube[@currency='$fromCode']/@rate")[0] ?? '';
        $toValue = $xml->xpath("//c:Cube[@currency='$toCode']/@rate")[0] ?? '';

        return $amount * $this->convertWithBaseCurrency($fromCode, $toCode, self::BASE_CURRENCY, (string)$fromValue, (string)$toValue);
    }
}
