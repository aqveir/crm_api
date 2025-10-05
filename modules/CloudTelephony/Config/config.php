<?php

return [
    'name' => 'CloudTelephony',

    //Exotel Configurations
    'exotel' => [
        'call' => [
            'callback-url' => '/api/telephony/exotel/call/callback',
        ],
        'sms' => [
            'callback-url' => '/api/telephony/exotel/sms/callback',
        ]
    ],
    
    //Twilio Configurations
    'twilio' => [
        'call' => [
            'callback-url' => '/api/telephony/twilio/call/callback',
        ],
        'sms' => [
            'callback-url' => '/api/telephony/twilio/sms/callback',
        ]
    ]
];
