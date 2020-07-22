<?php

return [
    'name' => 'Core',

    'settings' => [
        //Cache settings
        'cache' => [
            'country' => [
                'key' => '_cache_country_key_',
                'duration_in_sec' => 86400,
            ],

            'currency' => [
                'key' => '_cache_currency_key_',
                'duration_in_sec' => 86400,
            ], 

            'lookup' => [
                'key' => '_cache_lookup_key_',
                'duration_in_sec' => 86400,
            ], 

            'organization' => [
                'key' => '_cache_organization_key_',
                'duration_in_sec' => 86400,
            ],
        ],

        //New Organization Settings
        'new_organization' => [
            'default_text' => 'System created.',
            'default_role' => [
                'key' => 'organization_admin',
                'display_value' => 'Organization Administrator', 
                'description' => 'Default role created by the system.',
                'privileges' => [
                    1, 2, 3, 4, 8, 7
                ]
            ],
        ],
    ]
];
