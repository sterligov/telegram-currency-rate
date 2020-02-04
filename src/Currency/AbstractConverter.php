<?php


namespace App\Currency;


use GuzzleHttp\Client;

abstract class AbstractConverter implements ConverterInterface
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $fromCode
     * @param $toCode
     * @param $fromValue
     * @param $toValue
     * @param $base
     * @return float|int|mixed
     * @throws \InvalidArgumentException
     */
    protected function convertWithBaseCurrency($fromCode, $toCode, $base, $fromValue, $toValue)
    {
        if (!$fromValue && $fromCode != $base) {
            throw new \InvalidArgumentException("Bad currency code $fromCode");
        }

        if (!$toValue && $toCode != $base) {
            throw new \InvalidArgumentException("Bad currency code $fromCode");
        }

        if (!$fromValue && !$toValue) {
            throw new \InvalidArgumentException("Bad currency value $fromCode, $toCode");
        }

        $fromValue = str_replace(',', '.', $fromValue);
        $toValue = str_replace(',', '.', $toValue);

        if ($fromCode == $base) {
            return $toValue;
        }

        if ($toCode == $base) {
            return 1 / $fromValue;
        }

        return $toValue / $fromValue;
    }
}