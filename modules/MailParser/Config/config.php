<?php

return [
    'name' => 'MailParser',

    'settings' => [
        'parse' => [
            'synonyms' => [
                //First Name
                'first_name' => [
                    'first_name', 'name_first', 
                    'firstname', 'firstName',
                    'FirstName', 'First_Name'
                ],

                //Last Name
                'last_name' => [
                    'last_name', 'name_last',
                    'lastname', 'lastName',
                    'LastName', 'Last_Name',
                    'surname', 'Surname'
                ],

                //Email
                'email' => [
                    'email', 'mail', 'email_address', 'e-mail',
                    'Email', 'EMail', 'E-Mail', 'E-mail'
                ],

                //Phone
                'phone' => [
                    'phone', 'phone_number',
                    'Phone'
                ],
            ],
        ]
    ]
];
