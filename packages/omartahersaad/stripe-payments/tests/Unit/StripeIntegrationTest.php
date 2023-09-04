<?php

namespace OmarTaherSaad\StripePayments\Tests\Unit;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use OmarTaherSaad\StripePayments\StripePayment;
use OmarTaherSaad\StripePayments\Tests\TestCase;

class StripeIntegrationTest extends TestCase
{
    //Test creating a new Stripe client
    public function test_create_stripe_client()
    {
        //Set config stripe-payments.secret_key to value
        Config::set('stripe-payments.secret_key', 'secretkey');
        $stripe = StripePayment::getClient();
        $this->assertInstanceOf(\Stripe\StripeClient::class, $stripe);
    }

    //Test creating checkout session
    public function test_creating_checkout_session()
    {
        Config::set('stripe-payments.secret_key', env('STRIPE_TEST_SECRET_KEY'));
        $orderUuid = 'order-uuid';
        // Test data
        $data = [
            'success_url' => url('test'),
            'cancel_url' => url('test'),
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Order #123',
                        ],
                        'unit_amount' => 10000,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            // Set currency from validated data
            'client_reference_id' => $orderUuid,
        ];
        $session = StripePayment::createCheckoutSession($data, $orderUuid);
        $this->assertInstanceOf(\Stripe\Checkout\Session::class, $session);
        $this->assertDatabaseHas('stripe_payment_requests', [
            'order_uuid' => $orderUuid,
            'checkout_session_id' => $session->id,
        ]);
    }

    //Test getting checkout session
    public function test_getting_checkout_session()
    {
        //First, create a new checkout session
        Config::set('stripe-payments.secret_key', env('STRIPE_TEST_SECRET_KEY'));
        $orderUuid = 'order-uuid';
        // Test data
        $data = [
            'success_url' => url('test'),
            'cancel_url' => url('test'),
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Order #123',
                        ],
                        'unit_amount' => 10000,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            // Set currency from validated data
            'client_reference_id' => $orderUuid,
        ];
        $session = StripePayment::createCheckoutSession($data, $orderUuid);

        //Then, try to get the checkout session
        $sessionRetrieved = StripePayment::getCheckoutSession($session->id);
        $this->assertInstanceOf(\Stripe\Checkout\Session::class, $sessionRetrieved);
    }
}
