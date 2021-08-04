<?php

return [
    'name' => 'Subscription',

    'settings' => [
        'stripe_secret_key' => env('STRIPE_SECRET'),

        //Cache settings
        'cache' => [
            'stripe_products' => [
                'key' => '_cache_stripe_products_key_',
                'duration_in_sec' => 86400,
            ],

            'stripe_prices' => [
                'key' => '_cache_stripe_prices_key_',
                'duration_in_sec' => 86400,
            ],
        ],
    ]
];
