<?php

namespace OmarTaherSaad\StripePayments;

use Illuminate\Support\Facades\Log;

class PaymentCore
{
    //Package version
    public const VERSION = '1.0.0';

    //Stripe API version [https://stripe.com/docs/upgrades#2023-08-16]
    public const STRIPE_API_VERSION = '2023-08-16';

    //Create a new Stripe client
    public static function getClient(): ?\Stripe\StripeClient
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
}
