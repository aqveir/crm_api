<?php


return [

    'table_name' => [
        //Common Entities
        'countries' => env('TABLE_NAME_PREFIX', '') . 'countries',
        'currencies' => env('TABLE_NAME_PREFIX', '') . 'currencies',
        'timezones' => env('TABLE_NAME_PREFIX', '') . 'timezones',

        'backend_menus' => env('TABLE_NAME_PREFIX', '') . 'backend_menus',
        'configurations' => env('TABLE_NAME_PREFIX', '') . 'configurations',

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

        //Backend User Entities
        'user' => [
            'main' => env('TABLE_NAME_PREFIX', '') . 'users',
            'availability' => env('TABLE_NAME_PREFIX', '') . 'user_availability',
            'availability_history' => env('TABLE_NAME_PREFIX', '') . 'user_availability_history',
            'roles' => env('TABLE_NAME_PREFIX', '') . 'user_roles',
            'privileges' => env('TABLE_NAME_PREFIX', '') . 'user_privileges',            
        ],
        
        //Common Application Entities
        'notes' => env('TABLE_NAME_PREFIX', '') . 'notes',
        'images' => env('TABLE_NAME_PREFIX', '') . 'images',
        'feedbacks' => env('TABLE_NAME_PREFIX', '') . 'feedbacks',
        'documents' => env('TABLE_NAME_PREFIX', '') . 'documents',

        //Customer Entities
        'customer' => [
            'main' => env('TABLE_NAME_PREFIX', '') . 'customers',
            'details' => env('TABLE_NAME_PREFIX', '') . 'customer_details',
            'addresses' => env('TABLE_NAME_PREFIX', '') . 'customer_addresses',
            'company' => env('TABLE_NAME_PREFIX', '') . 'customer_company',
            
            'society_address' => env('TABLE_NAME_PREFIX', '') . 'society_address',
            'apartment_address' => env('TABLE_NAME_PREFIX', '') . 'apartment_address',
        ],

        'crm' => [
            'agency' => [
                'main' => env('TABLE_NAME_PREFIX', '') . 'agency',
            ],
           
        ],
    ],
];