<?php

namespace OmarTaherSaad\StripePayments\Http\Controllers;

use OmarTaherSaad\StripePayments\Http\Requests\PerformPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
                'session_id' => '{CHECKOUT_SESSION_ID}'
            ]),
            'cancel_url' => route('stripe-payment.callback', [
                'status' => 'cancel',
                'session_id' => '{CHECKOUT_SESSION_ID}'
            ]),
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
            $stripe = new \Stripe\StripeClient([
                'api_key' => config('stripe-payments.secret_key'),
                'stripe_version' => StripePayment::STRIPE_API_VERSION,
            ]);
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
}
