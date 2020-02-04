<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 10/23/19
 * Time: 8:07 PM
 */

namespace App\Tests;


class EuropeanCentralBankTest extends RussianCentralBankTest
{
    const XML_TEST_DATA = 'european_bank.xml';

    protected function setUp(): void
    {
        $this->converter = new \App\Currency\EuropeanCentralBank($this->getHttpClientMock());
    }
}