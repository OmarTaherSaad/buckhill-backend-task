<?php

namespace OmarTaherSaad\CurrenTune;


class CurrenTune
{
    //Package version
    const VERSION = '1.0.0';

    /**
     * Get the latest currency rates from the European Central Bank
     */
    private static function getRates()
    {
        $url = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';
        $xml = simplexml_load_file($url);
        $dataArray = json_decode(json_encode($xml), true);
        $ratesArray = $dataArray['Cube']['Cube']['Cube'];
        $rates = [];
        foreach ($ratesArray as $rate) {
            $rates[strtolower($rate['@attributes']['currency'])] = $rate['@attributes']['rate'];
        }
        return $rates;
    }

    /**
     * Get the latest rate for a currency
     * @param string $currency The currency to get the rate for (e.g. USD)
     */
    public static function getRate($currency)
    {
        $rates = self::getRates();
        return $rates[strtolower($currency)] ?? false;
    }

    /**
     * Convert an amount from EUR to another currency
     * @param float $amount The amount to convert in EUR
     * @param string $toCurrency The currency to convert to (e.g. USD)
     */
    public static function convert($amount, $toCurrency)
    {
        return $amount * self::getRate($toCurrency);
    }
}
