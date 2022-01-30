<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Third Party Auth Services - Socialite
    |--------------------------------------------------------------------------
    |
    | This section is for storing the credentials for third party services such
    | as Facebook, Google, LinkedIn and more. This file provides the details for
    | accessing the social authentication information, allowing providers to have
    | a conventional way to authenticate the customers.
    |
    */
    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_CLIENT_REDIRECT', 'http://your-callback-url'),
    ],
    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID', '1354596034729954'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET', 'a4182a1236d4ff9c1820f6799b472c7b'),
        'redirect' => env('FACEBOOK_CLIENT_REDIRECT', 'https://crmomni.com/api/contact/login/facebook/callback'),
    ],
    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'redirect' => env('TWITTER_CLIENT_REDIRECT', 'http://your-callback-url'),
    ],
    'linkedin' => [
        'client_id' => env('LINKEDIN_CLIENT_ID'),
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
        'redirect' => env('LINKEDIN_CLIENT_REDIRECT', 'http://your-callback-url'),
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_CLIENT_REDIRECT', 'http://your-callback-url'),
    ],

];
