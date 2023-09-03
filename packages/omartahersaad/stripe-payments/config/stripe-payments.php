<?php

return [
    'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),

    'secret_key' => env('STRIPE_SECRET_KEY'),

    'path-prefix' => env('STRIPE_PATH_PREFIX', 'stripe-payments'),
];
