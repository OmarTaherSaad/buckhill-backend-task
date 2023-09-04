<?php

namespace OmarTaherSaad\StripePayments;

use Illuminate\Support\Facades\Log;
use OmarTaherSaad\StripePayments\Models\StripePaymentRequest;

class StripePayment
{
    //Package version
    const VERSION = '1.0.0';

    //Stripe API version [https://stripe.com/docs/upgrades#2023-08-16]
    const STRIPE_API_VERSION = '2023-08-16';

    //Create a new Stripe client
    public static function getClient()
    {
        return new \Stripe\StripeClient([
            'api_key' => config('stripe-payments.secret_key'),
            'stripe_version' => StripePayment::STRIPE_API_VERSION,
        ]);
    }

    //Get Checkout session by ID
    public static function getCheckoutSession($sessionId): ?\Stripe\Checkout\Session
    {
        try {
            $stripe = StripePayment::getClient();
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
            $stripe = StripePayment::getClient();
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
     * @param \Stripe\Checkout\Session $session
     * @param StripePaymentRequest $stripePaymentRequest
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

            $isPaid = $session && $session->payment_status == 'paid';
            if ($isPaid) {
                //Get Charge to get payment method details
                $charge = StripePayment::getChargeFromCheckoutSession($session);
                //Save the payment
                $paymentModelClass = config('stripe-payments.payment_model');
                //Assume that payment is whether credit card or bank transfer [For task limitations sake]
                $paymentMethod = $charge->payment_method_details['type'] == 'card' ? 'credit_card' : 'bank_transfer';
                $payment = $paymentModelClass::create([
                    'type' => $paymentMethod,
                    'details' => $charge->payment_method_details->values(),
                ]);
                //Attach the payment to the order
                $order->payment()->associate($payment->id);
                $order->save();
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
}
