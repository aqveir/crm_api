<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Back-end URI prefix
    |--------------------------------------------------------------------------
    |
    | Specifies the URL name used for accessing back-end pages.
    | For example: backend -> http://localhost/backend
    |
    */
    'backendUri' => env('APPLICATION_BACKEND_URI', 'console'),

    'settings' => [
        /*
        |--------------------------------------------------------------------------
        | Specifies the default date format.
        |--------------------------------------------------------------------------
        */
        'date_format' => 'Y-m-d H:i:s',
        'date_format_response_generic' => 'c',

        'default' => [
            'role' => [
                'key_super_admin' => ['super_admin'],
                'key_organization_owner' => ['organization_owner']
            ]
        ],        

        /*
        |--------------------------------------------------------------------------
        | Specifies the default cache settings by entities
        |--------------------------------------------------------------------------
        */
        'cache' => [
            'user' => [
                'key' => '_cache_user_key_',
                'duration_in_sec' => 86400,
            ],

            'catalogue' => [
                'category' => [
                    'key' => '_cache_catalogue_category_key_',
                    'duration_in_sec' => 86400,
                ]
            ]
        ],

        'http_status_code' => [
            'success' => 200
        ],

        'static' => [
            'key' => [
                'lookup_value' => [
                    //Customer Details Types
                    'phone' => 'customer_detail_type_phone',
                    'email' => 'customer_detail_type_email',

                    //Customer Address Types
                    'home'  => 'customer_address_type_home',
                    'work'  => 'customer_address_type_work'
                ]
            ],
            'value' => [

            ],
        ],

        'external_data' => [
            //Currency exchange rates
            'currency_exchange' => [
                'base_uri' => 'https://api.ratesapi.io/api',
                'timeout' => 60
            ],

            
        ],
    ],
];