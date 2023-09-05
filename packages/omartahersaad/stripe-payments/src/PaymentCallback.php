<?php

namespace OmarTaherSaad\StripePayments;

use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;
use OmarTaherSaad\StripePayments\Models\StripePaymentRequest;

class PaymentCallback extends PaymentCore
{
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
    public static function handlePaymentStatus(?Session $session, StripePaymentRequest $stripePaymentRequest): bool
    {
        $isPaid = false;
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
        } catch (\Exception $e) {
            //Log error
            Log::error($e->getMessage());
            //Assume that payment is failed
            $stripePaymentRequest->update([
                'callback_payload' => $session?->values() ?? [],
                'status' => 'failure',
            ]);
        } finally {
            return $isPaid;
        }
    }

    public function savePayment($order, $charge, $paymentMethod): void
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
