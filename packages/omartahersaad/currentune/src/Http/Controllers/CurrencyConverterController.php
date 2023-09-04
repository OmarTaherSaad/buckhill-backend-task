<?php

namespace OmarTaherSaad\CurrenTune\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use OmarTaherSaad\CurrenTune\CurrenTune;

class CurrencyConverterController extends Controller
{
    public function convert(Request $request)
    {
        $data = $request->validate([
            'amount'    => 'required|numeric|min:0.01',
            'to_currency' => 'required|string|size:3',
        ]);
        $rate = CurrenTune::getRate($data['to_currency']);
        return response()->json([
            'success' => true,
            'data' => [
                'amount' => $data['amount'],
                'to_currency' => $data['to_currency'],
                'exchange_rate' => $rate,
                'converted_amount' => number_format($rate * $data['amount'], 3, '.', ''),
                // Format the converted amount to 2 decimal places & the European style of using a comma as the decimal separator
                'converted_amount_pretty' => number_format($rate * $data['amount'], 2, ',', '.'),
            ]
        ]);
    }
}
