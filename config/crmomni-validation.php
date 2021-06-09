<?php

use Illuminate\Validation\Rule;

return [
    'request_handler' => [
        'backend' => [
            // Organization Controller Requests
            'organization' => [
                // Create Organization
                'create' => [
                    'validation_rules' => [
                        'name' => 'nullable|string|max:40',
                        'subdomain' => 'required|max:36|unique:' . config('crmomni-migration.table_name.organizations') . ',subdomain',
                        'first_name' => 'required|string|max:80',
                        'last_name' => 'string|max:80',
                        'contact_person_name' => 'required|string|max:80',
                        'email' => 'required|email|max:40|unique:users,email',
                        'phone' => 'nullable|string|max:15',
                        'phone_idd' => 'required_with:phone|string|max:5',
                        'industry_key' => 'string'
                    ]
                ],

                // Update Organization
                'update' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',

                        'logo' => 'nullable|image|mimes:jpg,png|dimensions:ratio=1/1|max:512',
                        'name' => 'nullable|string|max:40',
                        'contact_person_name' => 'required|string|max:80',
                        'email' => 'required|email|max:40|unique:users,email',
                        'phone' => 'nullable|string|max:15',
                        'phone_idd' => 'required_with:phone|string|max:5',
                        'industry_key' => 'string',

                        'address' => 'nullable|string',
                        'locality' => 'nullable|string',
                        'city' => 'nullable|string',
                        'state_id' => 'nullable|numeric',
                        'country_id' => 'nullable|numeric',
                        'zipcode' => 'nullable|string',
                        'google_place_id' => 'nullable|string',
                        'latitude' => 'nullable|numeric',
                        'latitude' => 'nullable|numeric',

                        'is_active' => 'nullable|boolean',
                    ]
                ],

                //Delete Organization
                'delete' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                    ]
                ]
            ], //Organization Controller ends

            // LookUp Controller Requests
            'lookup' => [
                // Lookup data fetch-show
                'fetch' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                    ]
                ],

                // LookUp Create Request
                'create' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                        'name' => 'required|max:25',
                        'description' => 'max:40'
                    ]
                ],

                // LookUp Update Request
                'update' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                        
                        'id' => 'required|numeric',
                        'name' => 'required|max:25',
                        'description' => 'nullable|max:250'
                    ]
                ],
            ], // LookUp Controller ends

            // Privilege Controller Requests
            'privilege' => [
                //Fetch all privileges
                'fetch' => [
                    'validation_rules' => [ ]
                ],

                // Privilege Create Request
                'create' => [
                    'validation_rules' => [
                        'name' => 'required|max:25',
                        'description' => 'max:40'
                    ]
                ],

                // Privilege Update Request
                'update' => [
                    'validation_rules' => [
                        'id' => 'required|numeric',
                        'name' => 'required|max:25',
                        'description' => 'nullable|max:250'
                    ]
                ],
            ], // Privilege Controller ends

            // Role Controller Requests
            'role' => [
                // Role fetch-show
                'fetch' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                    ]
                ],

                // Role Create Request
                'create' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',

                        'display_value' => 'required|max:255',
                        'description' => 'max:255',
                        'privileges' => 'required|array',
                    ]
                ],

                // Role Update Request
                'update' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',

                        'display_value' => 'required|max:255',
                        'description' => 'max:255',
                        'privileges' => 'required|array',
                    ]
                ],
            ], //Role Controller ends

            // User Auth Controller Requests
            'auth' => [
                //Authenticate user
                'login' => [
                    'validation_rules' => [
                        'username' => 'required|email|min:6|max:40',
                        'password' => 'required|string|min:6'
                    ]
                ],

                //Logout user
                'logout' => [
                    'validation_rules' => [
                    ]
                ],

                // Forgot Password Request for User
                'forgot_password' => [
                    'validation_rules' => [
                        'email' => 'required|email|max:40|exists:' . config('crmomni-migration.table_name.user.main') . ',email'
                    ]
                ],

                // Change Password Request for User
                'change_password' => [
                    // This option must be set to true if you want to release a token
                    'release_token' => env('PASSWORD_RESET_RELEASE_TOKEN', false),

                    'validation_rules' => [
                        'old_password' => 'required|string|min:6',
                        'new_password' => 'required|confirmed|string|min:6'
                    ]
                ],

                //Reset password
                'reset_password' => [
                    // This option must be set to true if you want to release a token
                    'release_token' => env('PASSWORD_RESET_RELEASE_TOKEN', false),

                    'validation_rules' => [
                        'token' => 'required',
                        'email' => 'required|email|max:40',
                        'password' => 'required|confirmed|string|min:6'
                    ]
                ],
            ], //Auth Controller ends

            // User Controller Requests
            'user' => [
                // User Registration Request
                'register' => [
                    'validation_rules' => [
                        'first_name' => 'required|max:40',
                        'last_name' => 'nullable|max:40',
                        'email' => 'required|email|max:40|unique:users,email',
                        'phone' => 'required|string|max:15',
                        'phone_idd' => 'required_with:phone|string|max:5',
                    ]
                ],

                // User Creation Request
                'create' => [
                    // This option must be set to true if you want to release a token
                    'release_token' => env('SIGN_UP_RELEASE_TOKEN', false),

                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                        
                        'username' => 'required|max:36|unique:users,username',
                        'password' => 'required|string|confirmed|min:8|max:99',
                        'first_name' => 'required|max:40',
                        'last_name' => 'nullable|max:40',
                        'email' => 'required|email|max:40|unique:users,email',
                        'phone' => 'string|max:15',
                        'phone_idd' => 'required_with:phone|string|max:5',
                        'language' => 'required|string',
                        'roles' => 'required|array',
                        'roles.*.key' => 'required|string|max:100',
                        'roles.*.account_id' => 'nullable|integer',
                        'roles.*.description' => 'nullable|string'
                    ]
                ],

                //User Update Request
                'update' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',

                        'avatar' => 'nullable|image|mimes:jpg,bmp,png|dimensions:ratio=1/1|max:512', //Max size in KB
                        'first_name' => 'required|string|max:40',
                        'last_name' => 'string|max:40',
                        'email' => 'email|max:40',
                        'phone' => 'string|max:15',
                        'phone_idd' => 'required_with:phone|string|max:5',
                        'language' => 'required|string',
                        'roles' => 'required|array',
                        'roles.*.key' => 'required|string|max:100',
                        'roles.*.account_id' => 'nullable|integer',
                        'roles.*.description' => 'nullable|string'
                    ]
                ],

                //User Delete Request
                'delete' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                    ]
                ],

                // Check Existing User Request
                'exists' => [
                    'validation_rules' => [
                        'username' => 'required_without_all:email,phone|max:40',
                        'email' => 'required_without_all:username,phone|email|max:40',
                        'phone' => 'required_without_all:username,email|number|max:40'
                    ]
                ],

                // Verify User Request
                'verify' => [
                    'validation_rules' => [
                        'email' => 'required|email|max:40|exists:' . config('crmomni-migration.table_name.user.main') . ',email',
                    ]
                ],

                // Activate User Account Request
                'activate' => [
                    'validation_rules' => [
                        'email' => 'required|email|max:40|exists:' . config('crmomni-migration.table_name.user.main') . ',email',
                    ]
                ],

                //Get User data
                'fetch' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                    ]
                ],

                //User Status Request: Intended for telecallers
                'status' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                        
                        'role' => 'required|string',
                    ]
                ],

                // User Availability
                'availbility' => [
                    'validation_rules' => []
                ],
            ], // User Controller end

            // Note Controller Requests
            'note' => [
                // Create Note validations
                'create' => [
                    'validation_rules' => [
                        'entity_type'     => 'required|string|exists:' . config('crmomni-migration.table_name.lookup_value') . ',key',
                        'reference_id' => 'required|numeric',
                        'note' => 'required|string|max:1000',
                    ]
                ],

                // Update Note validations
                'update' => [
                    'validation_rules' => [
                        'note' => 'required|string|max:1000',
                    ]
                ],

                // Delete Note validations
                'delete' => [
                    'validation_rules' => [
                    ]
                ]
            ], // Note Controller end

            // Document Controller Requests
            'document' => [
                // Create Document validations
                'create' => [
                    'validation_rules' => [
                        'entity_type'     => 'required|string|exists:' . config('crmomni-migration.table_name.lookup_value') . ',key',
                        'reference_id'    => 'required|numeric',
                        'title'           => 'required|string|max:150',
                        'description'     => 'string|max:1000',
                        'files.*'         => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,xls,xlsx,zip,rar,txt'
                    ]
                ],

                // Update Document validations
                'update' => [
                    'validation_rules' => [
                        'title'           => 'string|max:1000',
                        'description'     => 'string|max:1000',
                    ]
                ],

                // Delete Document validations
                'delete' => [
                    'validation_rules' => []
                ],
            ], //Document Controller end

            // Contact Controller Requests
            'contact' => [
                'fetch' => [
                    'validation_rules' => []
                ],

                'create' => [
                    'validation_rules' => []
                ],

                'update' => [
                    'validation_rules' => []
                ],

                'delete' => [
                    'validation_rules' => []
                ],

                'upload' => [
                    'validation_rules' => [
                        'files.*' => 'required|file|mimes:csv,txt,xls,xlsx|max:1024'
                    ]
                ],

                // Telephony
                'telephony' => [
                    'validation_rules' => []
                ],
            ], // Contact Controller end

            // Subscription Controller Requests
            'subscription' => [
                // Create Subscription validations
                'create' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|max:255|unique:'. config('crmomni-migration.table_name.subscription.main') .',key',
                        
                        'display_value' => 'required|string|max:255',
                        'description' => 'sometimes|string|max:1000',
                        'data_json' => 'sometimes|json',
                        'order' => 'sometimes|number',
                        'is_displayed' => 'required|boolean'
                    ]
                ],

                // Update Subscription validations
                'update' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|max:255|unique:'. config('crmomni-migration.table_name.subscription.main') .',key',
                        
                        'display_value' => 'required|string|max:255',
                        'description' => 'sometimes|string|max:1000',
                        'data_json' => 'sometimes|json',
                        'order' => 'sometimes|number',
                        'is_displayed' => 'required|boolean'
                    ]
                ],

                // Delete Subscription validations
                'delete' => [
                    'validation_rules' => [
                    ]
                ]
            ], // Subscription Controller end

            // Preferences Controller Requests
            'preference' => [
                //Fetch all privileges
                'fetch' => [
                    'validation_rules' => [ 
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                    ]
                ],

                // Preferences Create Request
                'create' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',

                        'name' => [
                            'required',
                            'string',
                            'max:100',
                            Rule::unique('preferences', 'name')
                        ],
                        'display_value' => 'required|string|max:100',
                        'description' => 'nullable|string|max:1000', 
                        'column_name' => 'nullable|string|max:100',
                        'is_minimum' => 'sometimes|boolean', 
                        'is_maximum' => 'sometimes|boolean', 
                        'is_multiple' => 'sometimes|boolean', 
                        'keywords' => 'sometimes|string', 
                        'order' => 'sometimes|numeric', 
                        'type_key' => 'required|string|exists:' . config('crmomni-migration.table_name.lookup_value') . ',key',
                        'data' => 'sometimes',
                        'data.values' => 'required_with:data|array'
                    ]
                ],

                // Preferences Update Request
                'update' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',

                        'display_value' => 'required|string|max:100',
                        'description' => 'sometimes|string|max:1000', 
                        'column_name' => 'sometimes|string|max:100',
                        'is_minimum' => 'sometimes|boolean', 
                        'is_maximum' => 'sometimes|boolean', 
                        'is_multiple' => 'sometimes|boolean', 
                        'keywords' => 'sometimes|string', 
                        'order' => 'sometimes|numeric', 
                        'type_key' => 'required|string|exists:' . config('crmomni-migration.table_name.lookup_value') . ',key',
                        'data' => 'sometimes',
                        'data.values' => 'required_with:data|array'
                    ]
                ],

                // Preferences Delete Request
                'delete' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45'
                    ]
                ]
            ], // Preferences Controller end

            // ServiceRequest Controller Requests
            'service_request' => [
                // ServiceRequest Fetch request
                'fetch' => [
                    'validation_rules' => [
                        'category_key' => 'required|string',
                        
                        'page' => 'numeric',
                        'size' => 'numeric'
                    ]
                ],

                // ServiceRequest Create Request
                'create' => [
                    'validation_rules' => [
                        'contact_hash' => 'required|string',
                        'account_hash' => 'nullable|string',
                        'service_request' => 'nullable',
                        'service_request.category_key' => 'nullable|string',
                        'service_request.type_key' => 'nullable|string',
                        'service_request.status_key' => 'nullable|string',
                        'service_request.stage_key' => 'nullable|string',
                        'service_request.search_tags' => 'nullable|string',
                        'service_request.star_rating' => 'nullable|integer|min:0|max:5',
                        'service_request.preferences' => 'nullable|array',
                        'service_request.owner_hash' => 'nullable|string'
                    ]
                ],

                // ServiceRequest Update request
                'update' => [
                    'validation_rules' => [
                        'account_hash' => 'nullable|string',
                        'service_request' => 'nullable',
                        'service_request.category_key' => 'nullable|string',
                        'service_request.type_key' => 'nullable|string',
                        'service_request.status_key' => 'nullable|string',
                        'service_request.stage_key' => 'nullable|string',
                        'service_request.search_tags' => 'nullable|string',
                        'service_request.star_rating' => 'nullable|integer|min:0|max:5',
                        'service_request.preferences' => 'nullable|array',
                        'service_request.owner_hash' => 'nullable|string'
                    ]
                ],

                // ServiceRequest Delete request
                'delete' => [
                    'validation_rules' => []
                ]
            ], // ServiceRequest Controller end

            // Account Controller Requests
            'account' => [
                // Account fetch-show
                'fetch' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                    ]
                ],

                // Account Create Request
                'create' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',

                        'name' => 'required|string|max:255',
                        'description' => 'nullable|string|max:255',
                        'type_key' => 'required|string',

                        'address' => 'nullable|string', 
                        'locality' => 'nullable|string', 
                        'city' => 'nullable|string', 
                        'state_id'  => 'nullable|numeric', 
                        'country_id'  => 'nullable|numeric', 
                        'zipcode' => 'nullable|string',
                        'google_place_id' => 'nullable|string', 
                        'longitude' => 'nullable|numeric', 
                        'latitude' => 'nullable|numeric',

                        'website' => 'nullable|string', 
                        'email' => 'nullable|email', 
                        'phone_idd' => 'nullable|string', 
                        'phone' => 'nullable|string',

                        'is_default' => 'nullable|boolean'
                    ]
                ],

                // Account Update Request
                'update' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',

                        'name' => 'required|string|max:255',
                        'description' => 'nullable|string|max:255',
                        'type_key' => 'required|string',

                        'address' => 'nullable|string', 
                        'locality' => 'nullable|string', 
                        'city' => 'nullable|string', 
                        'state_id'  => 'nullable|numeric', 
                        'country_id'  => 'nullable|numeric', 
                        'zipcode' => 'nullable|string',
                        'google_place_id' => 'nullable|string', 
                        'longitude' => 'nullable|numeric', 
                        'latitude' => 'nullable|numeric',

                        'website' => 'nullable|string', 
                        'email' => 'nullable|email', 
                        'phone_idd' => 'nullable|string', 
                        'phone' => 'nullable|string',

                        'is_default' => 'nullable|boolean'
                    ]
                ],

                // Account Delete Request
                'delete' => [
                    'validation_rules' => [
                        'key' => 'sometimes|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                    ]
                ]
            ], // Account Controller end

            // Task Controller Requests
            'task' => [
                // Task Fetch request
                'fetch' => [
                    'validation_rules' => [                       
                        'page' => 'numeric',
                        'size' => 'numeric'
                    ]
                ],

                'create' => [
                    'validation_rules' => [                       
                        'sr_hash' => 'required|string',
                        'subject' => 'required|string|max:200',
                        'description' => 'string|max:1000',
                        'start_at' => 'required|date',
                        'end_at' => 'required|date',
                        'subtype_key' => 'required|string',
                        'priority_key' => 'required|string',
                        'status_key' => 'required|string',
                        'assignee' => 'required|array',
                        'assignee.*.participant_type_key' => 'required|string',
                        'assignee.*.participant_hash' => 'required|string',
                    ]
                ],

                'update' => [
                    'validation_rules' => [                       
                        'subject' => 'required|string|max:200',
                        'description' => 'string|max:1000',
                        'start_at' => 'required|date',
                        'end_at' => 'required|date',
                        'subtype_key' => 'required|string',
                        'priority_key' => 'required|string',
                        'status_key' => 'required|string',
                        'assignee' => 'required|array',
                        'assignee.*.participant_type_key' => 'required|string',
                        'assignee.*.participant_hash' => 'required|string',
                    ]
                ]
            ],

            // Event Controller Requests
            'event' => [
                // Event Fetch request
                'fetch' => [
                    'validation_rules' => [                       
                        'page' => 'numeric',
                        'size' => 'numeric'
                    ]
                ],

                'create' => [
                    'validation_rules' => [                       
                        'page' => 'numeric',
                        'size' => 'numeric'
                    ]
                ]
            ],

            // Communication Controller Requests
            'communication' => [
                'call' => [
                    // Outgoing Call or Make Call
                    'outgoing' => [
                        'validation_rules' => [
                        ]
                    ]
                ],

                'sms' => [
                    'send' => [
                        'validation_rules' => [
                            'sms_message' => 'required|string|max:200',
                        ]
                    ],
                ],

                'mail' => [
                    'send' => [
                        'validation_rules' => [
                            'email_cc' => 'sometimes|email|array', 
                            'email_subject' => 'required|string|max:200', 
                            'email_body' => 'required|string',
                        ]
                    ],
                ],
            ],
        ],

        'frontend' => [
            // Contact Controller Requests
            'contact' => [
                //Get the contact data
                'show' => [
                    'validation_rules' => [
                        'key' => 'required|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                    ]
                ],

                'exists' => [
                    'validation_rules' => [
                        'key' => 'required|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                        
                        'email' => 'required_without:phone|email|max:36',
                        'phone' => 'required_without:email|max:15',
                    ]
                ],

                'register' => [
                    'validation_rules' => [
                        'key' => 'required|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                        'password' => 'required|confirmed|string|min:8|max:64',
                        'first_name' => 'string|max:64',
                        'last_name' => 'string|max:64',
                        'email' => 'required|email|max:64',
                        'phone' => 'required|min:8|max:16',
                        'phone_idd' => 'required_with:phone|min:1|max:5'
                    ]
                ],

                // Authenticate the contact
                'login' => [
                    'validation_rules' => [
                        'key' => 'required|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                        'username' => 'required|max:36',
                        'password' => 'required|string|min:8|max:64',
                        'phone_idd' => 'required|min:1|max:5',
                        'device_id' => 'required|string'
                    ]
                ],

                // Logout the contact
                'logout' => [
                    'validation_rules' => [
                        'key' => 'required|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                    ]
                ],

                // Forgot Password Request for User
                'forgot_password' => [
                    'validation_rules' => [
                        'key' => 'required|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                        'email' => 'required|email'
                    ]
                ],

                // Change Password Request for User
                'change_password' => [
                    // This option must be set to true if you want to release a token
                    'release_token' => env('PASSWORD_RESET_RELEASE_TOKEN', false),

                    'validation_rules' => [
                        'old_password' => 'required|string|min:8|max:64',
                        'new_password' => 'required|confirmed|string|min:8|max:64'
                    ]
                ],

                //Reset password
                'reset_password' => [
                    // This option must be set to true if you want to release a token
                    'release_token' => env('PASSWORD_RESET_RELEASE_TOKEN', false),

                    'validation_rules' => [
                        'token' => 'required',
                        'email' => 'required|email',
                        'password' => 'required|confirmed'
                    ]
                ],

                //Social authentication
                'social_login' => [
                    'validation_rules' => [
                        'key' => 'required|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                    ]
                ],
                'social_login_callback' => [
                    'validation_rules' => [
                        'key' => 'required|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                    ]
                ],

            ], // Contact Controller ends
        ],

        'telephony' => [
            //Exotel Provider
            'exotel' => [
                //Voice-Call Handler
                'voice' => [
                    'callback' => [
                        'validation_rules' => [
                            'key' => 'required|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                        ]
                    ],

                    'details' => [
                        'validation_rules' => [
                            'key' => 'required|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                        ]
                    ],

                    'passthru' => [
                        'validation_rules' => [
                            'key' => 'required|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:45',
                        ]
                    ],
                ],

                //Message-SMS Handler
                'sms' => [

                ]
            ]
        ]
    ]
];