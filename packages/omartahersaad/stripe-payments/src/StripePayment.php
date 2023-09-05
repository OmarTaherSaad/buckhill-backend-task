<?php

namespace OmarTaherSaad\StripePayments;

use Illuminate\Support\Facades\Log;
use OmarTaherSaad\StripePayments\Models\StripePaymentRequest;

class StripePayment
{
    //Package version
    public const VERSION = '1.0.0';

    //Stripe API version [https://stripe.com/docs/upgrades#2023-08-16]
    public const STRIPE_API_VERSION = '2023-08-16';

    //Create a new Stripe client
    public static function getClient()
    {
        try {
            return new \Stripe\StripeClient([
                'api_key' => config('stripe-payments.secret_key'),
                'stripe_version' => self::STRIPE_API_VERSION,
            ]);
        } catch (\Exception $e) {
            //Log error
            Log::error($e->getMessage());
            return null;
        }
    }

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

    public static function createSessionData($amount, $currency, $orderUuid)
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
                            'name' => "Order #$orderUuid",
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

    //Get Charge from Checkout session
    public static function getChargeFromCheckoutSession(\Stripe\Checkout\Session $session): ?\Stripe\Charge
    {
        try {
            $stripe = self::getClient();
            //Get Payment Intent
            $paymentIntent = $stripe->paymentIntents->retrieve($session->payment_intent);
            //Get Charge to get payment method details
            return $stripe->charges->retrieve($paymentIntent->latest_charge);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            //Log error
            Log::error($e->getMessage());
            return null;
        }
    }

    /**
     * Handle payment status & update order status from Checkout session
     * @return bool Whether the payment was successful or not
     */
    public static function handlePaymentStatus(
        ?\Stripe\Checkout\Session $session,
        StripePaymentRequest $stripePaymentRequest
    ): bool {
        try {
            //Get the order
            $orderModelClass = config('stripe-payments.order_model');
            $order = $orderModelClass::firstWhere('uuid', $stripePaymentRequest->order_uuid);

            $isPaid = $session && $session->payment_status === 'paid';
            if ($isPaid) {
                //Get Charge to get payment method details
                $charge = self::getChargeFromCheckoutSession($session);
                //Assume that payment is whether credit card or bank transfer [For task limitations sake]
                $paymentMethod = $charge->payment_method_details['type'] === 'card' ? 'credit_card' : 'bank_transfer';
                self::savePayment($order, $charge, $paymentMethod);
                //Order status must be handled by the user
            }
            $stripePaymentRequest->update([
                'callback_payload' => $session?->values() ?? [],
                'status' => $isPaid ? 'success' : 'failure',
                'payment_method' => $paymentMethod,
            ]);
            return $isPaid;
        } catch (\Exception $e) {
            //Log error
            Log::error($e->getMessage());
            //Assume that payment is failed
            $stripePaymentRequest->update([
                'callback_payload' => $session?->values() ?? [],
                'status' => 'failure',
            ]);
            return false;
        }
    }

    public function savePayment($order, $charge, $paymentMethod)
    {
        //Save the payment
        $paymentModelClass = config('stripe-payments.payment_model');
        $payment = $paymentModelClass::create([
            'type' => $paymentMethod,
            'details' => $charge->payment_method_details->values(),
        ]);
        //Attach the payment to the order
        $order->payment()->associate($payment->id);
        $order->save();
    }
}
