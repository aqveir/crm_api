<?php

return [
    'name' => 'MailParser',

    'settings' => [
        'parse' => [
            'synonyms' => [
                //First Name
                'first_name' => [
                    'first_name', 'name_first', 
                    'firstname', 'firstName'
                ],

                //Last Name
                'last_name' => [
                    'last_name', 'name_last',
                    'lastname', 'lastName'
                ],

                //Email
                'email' => [
                    'email', 'mail', 'email_address',
                    'e-mail'
                ],

                //Phone
                'phone' => [
                    'phone', 'phone_number'
                ],
            ],
        ]
    ]
];
