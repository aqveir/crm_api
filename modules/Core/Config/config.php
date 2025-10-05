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

        //Response transformation array
        'response_transform' => [
            'country' => [
                'properties' => [
                    'alpha2_code', 'alpha3_code', 'numeric_code', 'iso3166_2_code',
                    'display_value', 'display_official_name', 'official_domain_extn',
                    'currency_code', 'phone_idd_code'
                ]
            ]
        ],

        //New Organization Settings
        'new_organization' => [
            'default_text' => 'System created.',
            'default_roles' => [
                [ //Organization Admin
                    'key' => 'organization_admin',
                    'display_value' => 'Organization Administrator', 
                    'description' => 'Default role created by the system.',
                    'is_secure' => true,
                    'privileges' => [
                        //Manage Organization
                        'view_organization', 'edit_organization',

                        //Manage Privileges
                        'list_all_privileges',

                        //Manage Roles
                        'list_all_roles', 'view_role', 
                        'add_role', 'edit_role', 'delete_role',

                        //Manage Preferences
                        'list_all_organization_preferences', 'view_preference',
                        'add_preference', 'edit_preference', 'delete_preference',

                        //Manage Accounts
                        'list_all_organization_accounts', 'view_account',
                        'add_account', 'edit_account', 'delete_account',

                        //Manage Contact
                        'list_all_contacts', 'view_contact',
                        'add_contact', 'edit_contact', 'delete_contact', 
                        'show_contact_unmasked_data',

                        //Manage Service-Request
                        'list_all_servicerequests', 'view_servicerequest',
                        'add_servicerequest', 'edit_servicerequest', 'delete_servicerequest',

                        //Manage Task
                        'list_all_tasks', 'view_task',
                        'add_task', 'edit_task', 'delete_task',

                        //Manage Event
                        'list_all_events', 'view_event',
                        'add_event', 'edit_event', 'delete_event',

                        //Manage Notes
                        'add_note', 'edit_note', 'delete_note',

                        //Manage Documents
                        'add_new_document', 'delete_document',
                        
                        //Manage Catalogue
                        'list_all_organization_catalogue',
                        'add_new_catalogue_data', 'edit_catalogue_data',
                    ]
                ],
                [ //Account Admin
                    'key' => 'account_admin',
                    'display_value' => 'Account Administrator', 
                    'description' => 'Default account role created by the system.',
                    'is_secure' => true,
                    'privileges' => [
                        //Manage Accounts
                        'view_account', 'edit_account',

                        //Manage Contact
                        'list_account_contacts_only', 'view_contact',
                        'add_contact',

                        //Manage Service-Request
                        'list_account_servicerequests_only', 'view_servicerequest',
                        'add_servicerequest', 'edit_servicerequest', 'delete_servicerequest',

                        //Manage Task
                        'list_account_tasks_only', 'view_task',
                        'add_task', 'edit_task', 'delete_task',

                        //Manage Event
                        'list_account_events_only', 'view_event',
                        'add_event', 'edit_event', 'delete_event',

                        //Manage Notes
                        'add_note', 'edit_note', 'delete_note',

                        //Manage Documents
                        'add_new_document', 'delete_document'
                    ]
                ],
                [ //Telecaller
                    'key' => 'telecaller',
                    'display_value' => 'Telecaller', 
                    'description' => 'Default tele-caller role created by the system.',
                    'is_secure' => false,
                    'privileges' => [
                        //Manage Contact
                        'list_user_contacts_only', 'view_contact',

                        //Manage Service-Request
                        'list_user_servicerequests_only', 'view_servicerequest',
                        'add_servicerequest', 'edit_servicerequest',

                        //Manage Task
                        'list_user_tasks_only', 'view_task',
                        'add_task', 'edit_task',

                        //Manage Event
                        'list_user_events_only', 'view_event',
                        'add_event', 'edit_event',

                        //Manage Notes
                        'add_note',

                        //Manage Documents
                        'add_new_document',

                        'allow_call_outgoing',
                        'allow_call_incoming',
                        'allow_sms_outgoing',
                        'allow_msg_outgoing',
                        'allow_email_outgoing'
                    ]
                ],
                [ //Remote User
                    'key' => 'default_remote_user',
                    'display_value' => 'Remote User', 
                    'description' => 'Default remote user role created by the system.',
                    'is_secure' => true,
                    'privileges' => [
                        //Manage tele communication
                        'allow_call_incoming',
                        'allow_sms_incoming',
                        'allow_msg_incoming',
                        'allow_email_incoming',

                        //Manage email parsing
                        'allow_email_incoming_parsing'

                    ]
                ]
            ],
        ],
    ]
];
