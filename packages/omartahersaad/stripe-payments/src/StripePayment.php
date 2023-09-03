<?php

namespace OmarTaherSaad\StripePayments;

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
}
