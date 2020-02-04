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
     * @param $from
     * @param $to
     * @param $amount
     * @return mixed
     */
    public function convert($from, $to, $amount = 1);
}