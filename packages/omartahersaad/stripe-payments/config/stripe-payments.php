<?php

return [
    'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),

    'secret_key' => env('STRIPE_SECRET_KEY'),

    'path_prefix' => env('STRIPE_PATH_PREFIX', 'stripe-payments'),

    'order_model' => 'App\Models\Order',

    'payment_model' => 'App\Models\Payment',
];
