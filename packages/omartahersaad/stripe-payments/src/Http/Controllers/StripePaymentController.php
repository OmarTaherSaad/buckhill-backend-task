<?php

namespace OmarTaherSaad\StripePayments\Http\Controllers;

use OmarTaherSaad\StripePayments\Http\Requests\PerformPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use OmarTaherSaad\StripePayments\Models\StripePaymentRequest;
use OmarTaherSaad\StripePayments\StripePayment;

class StripePaymentController extends Controller
{
    public function pay(PerformPaymentRequest $request)
    {
        // Get validated data
        $data = $request->validated();
        // Prepare payment data
        $paymentData = [
            'success_url' => route('stripe-payment.callback', [
                'status' => 'success',
            ]) . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe-payment.callback', [
                'status' => 'cancel',
            ]) . '&session_id={CHECKOUT_SESSION_ID}',
            'line_items' => [
                [
                    'price_data' => [
                        // Set currency from validated data
                        'currency' => $data['currency'],
                        'product_data' => [
                            'name' => "Order #{$data['order_uuid']}",
                        ],
                        // Set amount from validated data and convert it to cents
                        'unit_amount' => $data['amount'] * 100,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            // Set currency from validated data
            'client_reference_id' => $data['order_uuid'],
        ];
        try {
            // Create a new Stripe client
            $stripe = StripePayment::getClient();
            // Create a new checkout session
            $session = $stripe->checkout->sessions->create($paymentData);
            // Create a new Stripe payment request and save session ID & payload in it
            StripePaymentRequest::create([
                'order_uuid' => $data['order_uuid'],
                'request_payload' => $paymentData,
                'checkout_session_id' => $session->id,
            ]);
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
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string|exists:stripe_payment_requests,checkout_session_id',
            'status' => 'required|string|in:success,cancel',
        ]);
        if ($validator->fails()) {
            return redirect()->route('home');
        }
        $data = $validator->validated();
        $stripePaymentRequest = StripePaymentRequest::firstWhere('checkout_session_id', $data['session_id']);
        //Get Checkout Session Status to check if the payment was really successful [Avoiding Fraud]
        $stripe = StripePayment::getClient();
        $session = $stripe->checkout->sessions->retrieve($data['session_id']);
        if ($session->payment_status !== 'paid') {
            $status = 'failure';
        } else {
            $status = 'success';
        }
        $stripePaymentRequest->update([
            'callback_payload' => $session->values(),
            'status' => $status,
        ]);
        // Redirect the user to the return page specified in the documentation
        $redirectUrl = url("payment/{$stripePaymentRequest->order_uuid}/?") . http_build_query([
            'status' => $status,
            'gtw' => 'stripe',
        ], '', '&');
        return redirect($redirectUrl);
    }
}
