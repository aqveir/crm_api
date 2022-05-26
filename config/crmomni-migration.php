<?php


return [

    'table_name' => [
        //Common Entities
        'countries' => env('TABLE_NAME_PREFIX', '') . 'countries',
        'currencies' => env('TABLE_NAME_PREFIX', '') . 'currencies',
        'timezones' => env('TABLE_NAME_PREFIX', '') . 'timezones',

        'backend_menus' => env('TABLE_NAME_PREFIX', '') . 'backend_menus',

        'configuration' => [
            'main' => env('TABLE_NAME_PREFIX', '') . 'configurations',
            'industry' => env('TABLE_NAME_PREFIX', '') . 'industry_configurations',
        ],

        //Organization Entity
        'organizations' => env('TABLE_NAME_PREFIX', '') . 'organizations',
        'organization_configurations' => env('TABLE_NAME_PREFIX', '') . 'organization_configurations',

        //Lookup Entities
        'lookup' => env('TABLE_NAME_PREFIX', '') . 'lookup',
        'lookup_value' => env('TABLE_NAME_PREFIX', '') . 'lookup_value',

        //Role and Privilege Entities
        'roles' => env('TABLE_NAME_PREFIX', '') . 'roles',
        'privileges' => env('TABLE_NAME_PREFIX', '') . 'privileges',
        'role_privileges' => env('TABLE_NAME_PREFIX', '') . 'role_privileges',

        //Subscription Entities
        'subscription' => [
            'main' => env('TABLE_NAME_PREFIX', '') . 'subscriptions',
            'plan' => env('TABLE_NAME_PREFIX', '') . 'subscription_plans',
            'items' => env('TABLE_NAME_PREFIX', '') . 'subscription_items',
            'organizations' => env('TABLE_NAME_PREFIX', '') . 'organization_subscriptions',
        ],

        //Account Entities
        'account' => [
            'main' => env('TABLE_NAME_PREFIX', '') . 'accounts',
        ],

        //Backend User Entities
        'user' => [
            'main' => env('TABLE_NAME_PREFIX', '') . 'users',
            'availability' => env('TABLE_NAME_PREFIX', '') . 'user_availability',
            'availability_history' => env('TABLE_NAME_PREFIX', '') . 'user_availability_history',
            'roles' => env('TABLE_NAME_PREFIX', '') . 'user_roles',
            'privileges' => env('TABLE_NAME_PREFIX', '') . 'user_privileges',
            'registration' => env('TABLE_NAME_PREFIX', '') . 'user_registration',
        ],
        
        //Common Application Entities
        'notes' => env('TABLE_NAME_PREFIX', '') . 'notes',
        'images' => env('TABLE_NAME_PREFIX', '') . 'images',
        'feedbacks' => env('TABLE_NAME_PREFIX', '') . 'feedbacks',
        'documents' => env('TABLE_NAME_PREFIX', '') . 'documents',

        //Contact Entities
        'company' => env('TABLE_NAME_PREFIX', '') . 'companies',
        'contact' => [
            'main' => env('TABLE_NAME_PREFIX', '') . 'contacts',
            'details' => env('TABLE_NAME_PREFIX', '') . 'contact_details',
            'addresses' => env('TABLE_NAME_PREFIX', '') . 'contact_addresses',
            'company' => env('TABLE_NAME_PREFIX', '') . 'contact_companies',
            
            'society_address' => env('TABLE_NAME_PREFIX', '') . 'society_address',
            'apartment_address' => env('TABLE_NAME_PREFIX', '') . 'apartment_address',
        ],

        //Service Request Entities
        'service_request' => [
            'main' => env('TABLE_NAME_PREFIX', '') . 'service-requests',
            'source' => env('TABLE_NAME_PREFIX', '') . 'service-request_sources',
            'source-data' => env('TABLE_NAME_PREFIX', '') . 'service-request_sources_data',
            'activity' => env('TABLE_NAME_PREFIX', '') . 'service-request_activities',
            'activity_participants' => env('TABLE_NAME_PREFIX', '') . 'service-request_activity_participants',
            'preference' => env('TABLE_NAME_PREFIX', '') . 'service-request_preferences',
            'communication' => env('TABLE_NAME_PREFIX', '') . 'service-request_communications',
        ],

        //Preference Entities
        'preference' => [
            'main' => env('TABLE_NAME_PREFIX', '') . 'preferences',
            'data' => env('TABLE_NAME_PREFIX', '') . 'preferences_data',
            'data_value' => env('TABLE_NAME_PREFIX', '') . 'preferences_data_values',
            'meta' => env('TABLE_NAME_PREFIX', '') . 'preferences_meta',
            'meta_industries' => env('TABLE_NAME_PREFIX', '') . 'preferences_meta_industries',
        ],

        'crm' => [
            'account' => [
                'main' => env('TABLE_NAME_PREFIX', '') . 'account',
            ],
        ],
    ],
];