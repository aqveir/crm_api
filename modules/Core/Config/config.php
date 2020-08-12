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
            'default_roles' => [
                [
                    'key' => 'organization_admin',
                    'display_value' => 'Organization Administrator', 
                    'description' => 'Default role created by the system.',
                    'privileges' => [
                        'read_organization_data',
                        'edit_organization_data',
                        'list_all_organization_accounts',
                        'add_new_account_data',
                        'delete_account_data',
                        'list_all_organization_customers',
                        'show_customer_unmasked_data',
                        'delete_note',
                        'delete_document'
                    ]
                ],
                [
                    'key' => 'account_admin',
                    'display_value' => 'Account Administrator', 
                    'description' => 'Default account role created by the system.',
                    'privileges' => [
                        'edit_account_data',
                        'add_new_customer_data',
                        'edit_customer_data',
                        'delete_customer_data',
                        'add_new_note',
                        'delete_document'
                    ]
                ],
                [
                    'key' => 'telecaller',
                    'display_value' => 'Telecaller', 
                    'description' => 'Default tele-caller role created by the system.',
                    'privileges' => [
                        'add_new_customer_data',
                        'edit_customer_data',
                        'add_new_note',
                        'allow_call_outgoing',
                        'allow_call_incoming',
                        'allow_sms_outgoing',
                        'allow_msg_outgoing',
                        'allow_email_outgoing'
                    ]
                ],
            ],
        ],
    ]
];
