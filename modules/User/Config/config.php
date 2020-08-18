<?php

return [
    'name' => 'User',

    'settings' => [
        //Cache settings
        'cache' => [
            'user' => [
                'key' => '_cache_user_key_',
                'duration_in_sec' => 86400,
            ],
        ],

        //New Organization Settings
        'new_organization' => [
            'default_text' => 'System created.',
            'default_password' => 'temp@1234',
            'email' => [
                'action_label' => 'Activate Account',
                'url' => '/user/activate/{activation_token}?email={user_email}&source=web'
            ]
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
