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

            'stripe_subscriptions' => [
                'key' => '_cache_stripe_subscriptions_key_',
                'duration_in_sec' => 86400,
            ]
        ],

        //Pricing settings
        'pricings' => [
            // STARTER Plan - Yearly
            'price_1JLAlSFoTpVJV8LyS6AhedR2' => [
                'product' => 'prod_Jz93V2RgCrizZ4',
                'nickname' => 'starter',
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
                    'setup_fee' => 'free',
                    'max_contacts' => 100,
                    'max_users' => 1,
                    'max_leads' => 250
                ]
            ],

            // STARTER Plan - Monthly
            'price_1JLAlSFoTpVJV8Ly2kv7wfDO' => [
                'product' => 'prod_Jz93V2RgCrizZ4',
                'nickname' => 'starter',
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
                    'setup_fee' => 'free',
                    'max_contacts' => 100,
                    'max_users' => 1,
                    'max_leads' => 250
                ]
            ],

            // BUSINESS Plan - Yearly
            'price_1JLB5oFoTpVJV8LyVhXgMV6C' => [
                'product' => 'prod_Jz9OvXD7Y7tL08',
                'nickname' => 'business',
                'billing_scheme' => 'per_unit',
                'unit_amount' => 200000,
                'unit_amount_decimal' => '200000',
                'currency' => 'inr',
                'recurring' => [
                    'aggregate_usage' => null,
                    'interval' => 'year',
                    'interval_count' => 1,
                    'trial_period_days' => null,
                    'usage_type' => 'licensed'
                ],
                'metadata' => [
                    'setup_fee' => 'free',
                    'customer_support' => 'free',
                    'max_contacts' => 250,
                    'max_users' => 5,
                    'max_leads' => 500
                ]
            ],

            // BUSINESS Plan - Monthly
            'price_1JLB5nFoTpVJV8LyrZHMQxoG' => [
                'product' => 'prod_Jz9OvXD7Y7tL08',
                'nickname' => 'business',
                'billing_scheme' => 'per_unit',
                'unit_amount' => 20000,
                'unit_amount_decimal' => '20000',
                'currency' => 'inr',
                'recurring' => [
                    'aggregate_usage' => null,
                    'interval' => 'month',
                    'interval_count' => 1,
                    'trial_period_days' => null,
                    'usage_type' => 'licensed'
                ],
                'metadata' => [
                    'setup_fee' => 'free',
                    'customer_support' => 'free',
                    'max_contacts' => 250,
                    'max_users' => 5,
                    'max_leads' => 500
                ]
            ],

            // EXECUTIVE Plan - Yearly
            'price_1JLApFFoTpVJV8LyZa5fVNM1' => [
                'product' => 'prod_Jz97oob5l6UQEe',
                'nickname' => 'executive',
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
                    'setup_fee' => 'free',
                    'customer_support' => 'free',
                    'max_contacts' => 250,
                    'max_users' => 5,
                    'max_leads' => 500
                ]
            ],

            // EXECUTIVE Plan - Monthly
            'price_1JLApFFoTpVJV8LyRQq4LLI2' => [
                'product' => 'prod_Jz97oob5l6UQEe',
                'nickname' => 'executive',
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
                    'setup_fee' => 'free',
                    'customer_support' => 'free',
                    'max_contacts' => 250,
                    'max_users' => 5,
                    'max_leads' => 500
                ]
            ],
        ],

        //Free plans array
        'free_plans' => ['price_1JLAlSFoTpVJV8Ly2kv7wfDO', 'price_1JLAlSFoTpVJV8LyS6AhedR2']
    ]
];
