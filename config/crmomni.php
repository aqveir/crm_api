<?php

return [

    'settings' => [

        /*
        |--------------------------------------------------------------------------
        | Back-end URI prefix
        |--------------------------------------------------------------------------
        |
        | Specifies the URL name used for accessing back-end pages.
        | For example: backend -> http://localhost/console
        |
        */
        'domain' => env('APPLICATION_DOAMIN', '{subdomain}.crmomni.com'),


        /*
        |--------------------------------------------------------------------------
        | Whitelisted Administration Subdomains
        |--------------------------------------------------------------------------
        |
        | Specifies the URL name used for accessing back-end pages.
        | For example: backend -> http://localhost/console
        |
        */
        'whitelisted_subdomains' => env('APPLICATION_WHITELIST_SUBDOAMIN', ['localhost', 'ellaisys']),


        /*
        |--------------------------------------------------------------------------
        | Back-end URI prefix
        |--------------------------------------------------------------------------
        |
        | Specifies the URL name used for accessing back-end pages.
        | For example: backend -> http://localhost/console
        |
        */
        'backend_uri' => env('APPLICATION_BACKEND_URI', '/console'),


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
            ],
            'account' => [
                'name' => 'Default',

            ]
        ],  

        
        /*
        |--------------------------------------------------------------------------
        | Specifies the default region for infrastucture setup
        |--------------------------------------------------------------------------
        */
        'regions' => [
            'crmomni.com' => [
                'database' => [
                    'url' => env('DATABASE_URL'),
                    'host' => '127.0.0.1',
                    'port' => '3306',
                    'database' => env('DB_DATABASE', 'eis_omnicrm'),
                    'username' => env('DB_USERNAME', 'eis_omnicrm_db_user'),
                    'password' => 'Ellaisys@123',
                ],
                'elastic' => [

                ]
            ],
            'crmomni.net' => [

            ],
            'crmomni.co.in' => [

            ],

            'default' => 'crmomni.com'
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
                    //Contact Details Types
                    'phone' => 'customer_detail_type_phone',
                    'email' => 'customer_detail_type_email',

                    //Contact Address Types
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