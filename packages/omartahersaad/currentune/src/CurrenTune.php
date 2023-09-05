<?php

namespace OmarTaherSaad\CurrenTune;

class CurrenTune
{
    //Package version
    public const VERSION = '1.0.0';

    /**
     * Get the latest currency rates from the European Central Bank
     * @return array An array of currency rates
     */
    private static function getRates(): array
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
     * @return float|bool The rate or false if the currency is not supported
     */
    public static function getRate(string $currency): float|bool
    {
        $rates = self::getRates();
        return $rates[strtolower($currency)] ?? false;
    }

    /**
     * Convert an amount from EUR to another currency
     * @param float $amount The amount to convert in EUR
     * @param string $toCurrency The currency to convert to (e.g. USD)
     */
    public static function convert(float $amount, string $toCurrency): float|bool
    {
        $rate = self::getRate($toCurrency);
        if (!$rate) {
            return false;
        }
        return $amount * $rate;
    }

    /**
     * Convert an amount from EUR to another currency and return detailed data
     * @param float $amount The amount to convert in EUR
     * @param string $toCurrency The currency to convert to (e.g. USD)
     * @return array|bool An array of data or false if the currency is not supported
     */
    public static function convertWithData(float $amount, string $toCurrency): array|bool
    {
        $rate = self::getRate($toCurrency);
        if (!$rate) {
            return false;
        }
        return [
            'amount' => $amount,
            'to_currency' => $toCurrency,
            'exchange_rate' => $rate,
            'converted_amount' => number_format($rate * $amount, 3, '.', ''),
            /** Format the converted amount to 2 decimal places
             * & the European style of using a comma as the decimal separator */
            'converted_amount_pretty' => number_format($rate * $amount, 2, ',', '.'),
        ];
    }
}
