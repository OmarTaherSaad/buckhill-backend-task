<?php

namespace OmarTaherSaad\CurrenTune\Tests\Unit;

use OmarTaherSaad\CurrenTune\CurrenTune;
use OmarTaherSaad\CurrenTune\Tests\TestCase;

class CurrencyConverterTest extends TestCase
{
    //Test getting rate for a currency
    public function test_getting_rate_for_a_currency()
    {
        $rate = CurrenTune::getRate('USD');
        $this->assertIsNumeric($rate);
    }

    //Test getting rate for a non existing currency
    public function test_getting_rate_for_a_non_existing_currency()
    {
        $rate = CurrenTune::getRate('XYZ');
        $this->assertFalse($rate);
    }

    //Test converting an amount from EUR to another currency
    public function test_converting_an_amount_from_EUR_to_another_currency()
    {
        $amount = CurrenTune::convert(10, 'GBP');
        $this->assertIsNumeric($amount);
    }

    //Test converting an amount from a non existing currency to another currency
    public function test_converting_an_amount_from_a_non_existing_currency_to_another_currency()
    {
        $amount = CurrenTune::convert(10, 'XYZ');
        $this->assertFalse($amount);
    }
}
