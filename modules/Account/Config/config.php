<?php

return [
    'name' => 'Account',

    'settings' => [
        //Cache settings
        'cache' => [
            'user' => [
                'key' => '_cache_account_key_',
                'duration_in_sec' => 86400,
            ],
        ],

        //New Organization Settings
        'new_organization' => [
            'account' => [
                'name' => 'Default',
                'default_text' => 'System created.',
                'account_type' => 'account_type_default',
                'email' => [
                    'action_label' => 'Activate Account',
                    'url' => '/user/activate/{activation_token}?email={user_email}&source=web'
                ]
            ],
        ],

        //Create User in Existing Organization
        'new_user' => [
            'email' => [
                'action_label' => 'Verify Email',
                'url' => '/user/verify/{activation_token}?key={org_hash}&email={user_email}&source=web'
            ]
        ]
    ]
];
