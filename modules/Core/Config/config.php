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

            'lookup_value' => [
                'key' => '_cache_lookup_value_key_',
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
                [ //Organization Admin
                    'key' => 'organization_admin',
                    'display_value' => 'Organization Administrator', 
                    'description' => 'Default role created by the system.',
                    'privileges' => [
                        'read_organization_data', 'edit_organization',

                        'list_all_organization_accounts',
                        'add_account', 'edit_account', 'delete_account',
                        
                        'list_all_organization_customers',
                        'delete_customer',
                        'show_customer_unmasked_data',

                        'add_note', 'edit_note', 'delete_note',

                        'add_document', 'view_document', 'delete_document'
                    ]
                ],
                [ //Account Admin
                    'key' => 'account_admin',
                    'display_value' => 'Account Administrator', 
                    'description' => 'Default account role created by the system.',
                    'privileges' => [
                        'edit_account_data',

                        'add_customer', 'edit_customer', 'delete_customer',

                        'add_note', 'edit_note', 'delete_note',

                        'add_document', 'view_document', 'delete_document'
                    ]
                ],
                [ //Telecaller
                    'key' => 'telecaller',
                    'display_value' => 'Telecaller', 
                    'description' => 'Default tele-caller role created by the system.',
                    'privileges' => [
                        'add_customer', 'edit_customer',

                        'add_note', 'edit_note',

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
