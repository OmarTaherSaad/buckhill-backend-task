<?php

namespace OmarTaherSaad\CurrenTune\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use OmarTaherSaad\CurrenTune\CurrenTune;
use Illuminate\Support\Facades\Validator;

class CurrencyConverterController extends Controller
{
    public function convert(Request $request)
    {
        //Make response JSON to return error messages
        $request->headers->set('Accept', 'application/json');
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'to_currency' => 'required|string|size:3',
        ]);
        $responseData = CurrenTune::convertWithData($data['amount'], $data['to_currency']);
        if ($responseData === false) {
            return response()->json([
                'success' => false,
                'message' => 'The currency you requested is not supported/valid.',
            ], 400);
        }
        return response()->json([
            'success' => true,
            'data' => $responseData,
        ]);
    }
}
