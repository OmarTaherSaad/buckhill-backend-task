<?php

namespace OmarTaherSaad\StripePayments\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use OmarTaherSaad\StripePayments\StripePayment;
use OmarTaherSaad\StripePayments\Models\StripePaymentRequest;
use OmarTaherSaad\StripePayments\Http\Requests\PerformPaymentRequest;

class StripePaymentController extends Controller
{
    public function pay(PerformPaymentRequest $request)
    {
        // Get validated data
        $data = $request->validated();
        try {
            // Create a new checkout session
            $session = StripePayment::createCheckoutSession($data, $data['order_uuid']);
            // Redirect the user to the checkout page
            return redirect($session->url);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function callback(Request $request)
    {
        //By default, payment is considered failed
        $status = 'failure';
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string|exists:stripe_payment_requests,checkout_session_id',
            'status' => 'required|string|in:success,cancel',
        ]);
        if ($validator->fails()) {
            // No return url is specified, so we will redirect the user to a default page
            return redirect(url("payment?") . http_build_query(['status' => $status, 'gtw' => 'stripe',], '', '&'));
        }
        $data = $validator->validated();
        //Get Request to check if it is still pending
        $stripePaymentRequest = StripePaymentRequest::firstWhere('checkout_session_id', $data['session_id']);
        if ($stripePaymentRequest->status == 'pending') {
            //Get Checkout Session Status to check if the payment was really successful [Avoiding Fraud]
            $checkoutSession = StripePayment::getCheckoutSession($data['session_id']);
            //Check if the payment was successful
            $isPaid = StripePayment::handlePaymentStatus($checkoutSession, $stripePaymentRequest);
            $status = $isPaid ? 'success' : 'failure';
        }
        // Redirect the user to the return page specified in the documentation
        $redirectUrl = url("payment/{$stripePaymentRequest->order_uuid}/?") . http_build_query([
            'status' => $status,
            'gtw' => 'stripe',
        ], '', '&');
        return redirect($redirectUrl);
    }
}
