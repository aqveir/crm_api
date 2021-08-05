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

        //Pricing settings
        'pricings' => [
            //FREE Plan - Yearly
            'price_1JKf4JSBhlzXkcAlExqqWolz' => [
                'product' => 'prod_Jyc6vZdfaztPyZ',
                'nickname' => 'Free Plan [Year]',
                'billing_scheme' => 'per_unit',
                'unit_amount' => 0,
                'currency' => 'inr',
                'recurring' => [
                    'aggregate_usage' => null,
                    'interval' => 'year',
                    'interval_count' => 1,
                    'trial_period_days' => null,
                    'usage_type' => 'licensed'
                ],
                'metadata' => [
                    'max_contacts' => 100,
                    'max_users' => 1,
                    'max_leads' => 250
                ]
            ],

            // FREE Plan - Monthly
            'price_1JKerzSBhlzXkcAlycNzCRMv' => [
                'product' => 'prod_Jyc6vZdfaztPyZ',
                'nickname' => 'Free Plan [Monthly]',
                'billing_scheme' => 'per_unit',
                'unit_amount' => 0,
                'currency' => 'inr',
                'recurring' => [
                    'aggregate_usage' => null,
                    'interval' => 'month',
                    'interval_count' => 1,
                    'trial_period_days' => null,
                    'usage_type' => 'licensed'
                ],
                'metadata' => [
                    'max_contacts' => 100,
                    'max_users' => 1,
                    'max_leads' => 250
                ]
            ],

            // BASIC Plan - Yearly
            'price_1JKf2ySBhlzXkcAlOkny81ou' => [
                'product' => 'prod_JycHKXZdhkDF7X',
                'nickname' => 'Basic Plan [Year]',
                'billing_scheme' => 'per_unit',
                'unit_amount' => 500000,
                'unit_amount_decimal' => '500000',
                'currency' => 'inr',
                'recurring' => [
                    'aggregate_usage' => null,
                    'interval' => 'year',
                    'interval_count' => 1,
                    'trial_period_days' => null,
                    'usage_type' => 'licensed'
                ],
                'metadata' => [
                    'max_contacts' => 250,
                    'max_users' => 5,
                    'max_leads' => 500
                ]
            ],

            // BASIC Plan - Monthly
            'price_1JKf2ySBhlzXkcAlAhuVyGvr' => [
                'product' => 'prod_JycHKXZdhkDF7X',
                'nickname' => 'Basic Plan [Monthly]',
                'billing_scheme' => 'per_unit',
                'unit_amount' => 50000,
                'unit_amount_decimal' => '50000',
                'currency' => 'inr',
                'recurring' => [
                    'aggregate_usage' => null,
                    'interval' => 'month',
                    'interval_count' => 1,
                    'trial_period_days' => null,
                    'usage_type' => 'licensed'
                ],
                'metadata' => [
                    'max_contacts' => 250,
                    'max_users' => 5,
                    'max_leads' => 500
                ]
            ],
        ]
    ]
];
