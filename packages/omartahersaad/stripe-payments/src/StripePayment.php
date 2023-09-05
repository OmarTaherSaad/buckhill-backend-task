<?php

namespace OmarTaherSaad\StripePayments;

use Illuminate\Support\Facades\Log;
use OmarTaherSaad\StripePayments\Models\StripePaymentRequest;

class StripePayment extends PaymentCore
{
    /**
     * Create a new Checkout session
     * @param array $paymentData
     */
    public static function createCheckoutSession(array $paymentData, string $orderUuid): \Stripe\Checkout\Session
    {
        // Create a new Stripe client
        $stripe = self::getClient();
        // Create a new checkout session
        $session = $stripe->checkout->sessions->create(self::createSessionData(
            $paymentData['amount'],
            $paymentData['currency'],
            $orderUuid
        ));
        // Create a new Stripe payment request and save session ID & payload in it
        StripePaymentRequest::create([
            'order_uuid' => $orderUuid,
            'request_payload' => $paymentData,
            'checkout_session_id' => $session->id,
        ]);
        return $session;
    }

    public static function createSessionData(float $amount, string $currency, string $orderUuid): array
    {
        // Prepare payment data
        return [
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
                        'currency' => $currency,
                        'product_data' => [
                            'name' => "Order #{$orderUuid}",
                        ],
                        // Set amount from validated data and convert it to cents
                        'unit_amount' => $amount * 100,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            // Set currency from validated data
            'client_reference_id' => $orderUuid,
        ];
    }

    //Get Checkout session by ID
    public static function getCheckoutSession($sessionId): ?\Stripe\Checkout\Session
    {
        try {
            $stripe = self::getClient();
            return $stripe->checkout->sessions->retrieve($sessionId);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            //Log error
            Log::error($e->getMessage());
            return null;
        }
    }
}
