<?php

namespace OmarTaherSaad\CurrenTune\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use OmarTaherSaad\CurrenTune\CurrenTune;

class CurrencyConverterController extends Controller
{
    public function convert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount'    => 'required|numeric|min:0.01',
            'to_currency' => 'required|string|size:3',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided.',
                'errors' => $validator->errors(),
            ], 400);
        }
        $data = $validator->validated();
        $rate = CurrenTune::getRate($data['to_currency']);
        if ($rate === false) {
            return response()->json([
                'success' => false,
                'message' => 'The currency you requested is not supported/valid.',
            ], 400);
        }
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
