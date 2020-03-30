<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 10/22/19
 * Time: 8:16 PM
 */

namespace App\Currency;


interface ConverterInterface
{
    /**
     * @param Currency $from
     * @param Currency $to
     * @param float $amount
     * @return mixed
     */
    public function convert(Currency $from, Currency $to, float $amount = 1.0);
}