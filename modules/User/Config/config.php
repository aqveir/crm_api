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
            'default_password' => 'temp@1234'
        ],
    ]
];
