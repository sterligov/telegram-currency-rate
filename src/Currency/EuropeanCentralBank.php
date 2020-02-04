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
     * @param $from
     * @param $to
     * @param $amount
     * @return float|int|mixed
     */
    public function convert($from, $to, $amount = 1)
    {
        $from = CodeConverter::toAlpha($from);
        $to = CodeConverter::toAlpha($to);

        $response = $this->client->get('https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml?34686d29f763ea6d2a8a22559b3df675');
        $xml = new \SimpleXMLElement($response->getBody()->getContents());
        $xml->registerXPathNamespace('c', 'http://www.ecb.int/vocabulary/2002-08-01/eurofxref');

        $fromValue = $xml->xpath("//c:Cube[@currency='$from']/@rate")[0] ?? '';
        $toValue = $xml->xpath("//c:Cube[@currency='$to']/@rate")[0] ?? '';

        return $amount * $this->convertWithBaseCurrency($from, $to, self::BASE_CURRENCY, (string)$fromValue, (string)$toValue);
    }
}