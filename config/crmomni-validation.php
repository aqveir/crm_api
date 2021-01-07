<?php

return [
    'request_handler' => [
        'backend' => [
            // Organization Controller Requests
            'organization' => [
                // Create Organization
                'create' => [
                    'validation_rules' => [
                        'name' => 'nullable|string|max:40',
                        'sub_domain' => 'required|max:36|unique:' . config('crmomni-migration.table_name.organizations') . ',sub_domain',
                        'first_name' => 'required|string|max:40',
                        'last_name' => 'nullable|string|max:40',
                        'email' => 'required|email|max:40|unique:users,email',
                        'phone' => 'nullable|string|max:15',
                        'country_idd' => 'required_with:phone|string|max:5',
                        'industry_type' => 'string'
                    ]
                ],
            ], //Organization Controller ends

            // LookUp Controller Requests
            'lookup' => [
                // Lookup data fetch-show
                'fetch' => [
                    'validation_rules' => [
                        'key' => 'required_without:sub_domain|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
                        'sub_domain' => 'required_without:key|string|exists:' . config('crmomni-migration.table_name.organizations') . ',sub_domain|max:36',
                    ]
                ],

                // LookUp Create Request
                'create' => [
                    'validation_rules' => [
                        'name' => 'required|max:25',
                        'description' => 'max:40'
                    ]
                ],

                // LookUp Update Request
                'update' => [
                    'validation_rules' => [
                        'id' => 'required|numeric',
                        'name' => 'required|max:25',
                        'description' => 'nullable|max:250'
                    ]
                ],
            ], // LookUp Controller ends

            // Privilege Controller Requests
            'privilege' => [
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
                        'key' => 'required_without:sub_domain|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
                        'sub_domain' => 'required_without:key|string|exists:' . config('crmomni-migration.table_name.organizations') . ',sub_domain|max:36',
                    ]
                ],

                // Role Create Request
                'create' => [
                    'validation_rules' => [
                        'key' => 'required_without:sub_domain|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
                        'sub_domain' => 'required_without:key|string|exists:' . config('crmomni-migration.table_name.organizations') . ',sub_domain|max:36',

                        'display_value' => 'required|max:255',
                        'description' => 'max:255',
                        'privileges' => 'required|array',
                    ]
                ],

                // Role Update Request
                'update' => [
                    'validation_rules' => [
                        'key' => 'required_without:sub_domain|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
                        'sub_domain' => 'required_without:key|string|exists:' . config('crmomni-migration.table_name.organizations') . ',sub_domain|max:36',

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
                        'key' => 'sometimes|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
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
                        'country_idd' => 'required_with:phone|string|max:5',
                    ]
                ],

                // User Creation Request
                'create' => [
                    // This option must be set to true if you want to release a token
                    'release_token' => env('SIGN_UP_RELEASE_TOKEN', false),

                    'validation_rules' => [
                        'key' => 'required_without:sub_domain|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
                        'sub_domain' => 'required_without:key|string|exists:' . config('crmomni-migration.table_name.organizations') . ',sub_domain|max:36',
                        
                        'username' => 'required|max:36|unique:users,username',
                        'password' => 'required|string|min:6',
                        'first_name' => 'nullable|max:40',
                        'last_name' => 'nullable|max:40',
                        'email' => 'required|email|max:40|unique:users,email',
                        'phone' => 'nullable|string|max:15',
                        'country_idd' => 'required_with:phone|string|max:5',
                        'roles' => 'required|array',
                    ]
                ],

                'update' => [
                    'validation_rules' => [
                        'key' => 'required_without:sub_domain|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
                        'sub_domain' => 'required_without:key|string|exists:' . config('crmomni-migration.table_name.organizations') . ',sub_domain|max:36',

                        'first_name' => 'string|max:40',
                        'last_name' => 'string|max:40',
                        'email' => 'email|max:40|unique:users,email',
                        'phone' => 'string|max:15',
                        'country_idd' => 'required_with:phone|string|max:5',
                    ]
                ],

                // Check Existing User Request
                'exists' => [
                    'validation_rules' => [
                        'user_name' => 'required|email|max:40'
                    ]
                ],

                // Verify User Request
                'verify' => [
                    'validation_rules' => [
                        'key' => 'required_without:sub_domain|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
                        'sub_domain' => 'required_without:key|string|exists:' . config('crmomni-migration.table_name.organizations') . ',sub_domain|max:36',

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
                        'key' => 'required_without:sub_domain|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
                        'sub_domain' => 'required_without:key|string|exists:' . config('crmomni-migration.table_name.organizations') . ',sub_domain|max:36',
                    ]
                ],

                //User Status Request: Intended for telecallers
                'status' => [
                    'validation_rules' => [
                        'key' => 'required_without:sub_domain|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
                        'role' => 'required|string',
                    ]
                ],

                // User Availability
                'availbility' => [
                    'validation_rules' => []
                ],
            ], // User Controller ends

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
                        'document'        => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,xls,xlsx,zip,rar,txt'
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
            ], //Document Controller ends

            // Contact Controller Requests
            'contact' => [
                'index' => [
                    'validation_rules' => [
                        'key' => 'required|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
                    ]
                ],

                // Telephony
                'telephony' => [
                    'validation_rules' => [
                        'key' => 'required|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
                    ]
                ],
            ],
        ],

        'frontend' => [
            // Contact Controller Requests
            'contact' => [
                //Get the contact data
                'show' => [
                    'validation_rules' => [
                        'key' => 'required|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
                    ]
                ],

                'exists' => [
                    'validation_rules' => [
                        'key' => 'required|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
                        'email' => 'required_without:phone|email|max:36',
                        'phone' => 'required_without:email|max:15',
                    ]
                ],

                'register' => [
                    'validation_rules' => [
                        'key' => 'required|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
                        'password' => 'required|confirmed|string|min:8|max:64',
                        'first_name' => 'string|max:64',
                        'last_name' => 'string|max:64',
                        'email' => 'required|email|max:64',
                        'phone' => 'required|min:8|max:16',
                        'country_idd' => 'required_with:phone|min:1|max:5'
                    ]
                ],

                // Authenticate the contact
                'login' => [
                    'validation_rules' => [
                        'key' => 'required|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
                        'username' => 'required|max:36',
                        'password' => 'required|string|min:8|max:64',
                        'country_idd' => 'required|min:1|max:5',
                        'device_id' => 'required|string'
                    ]
                ],

                // Logout the contact
                'logout' => [
                    'validation_rules' => [
                        'key' => 'required|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
                    ]
                ],

                // Forgot Password Request for User
                'forgot_password' => [
                    'validation_rules' => [
                        'key' => 'required|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
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
                        'key' => 'required|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
                    ]
                ],
                'social_login_callback' => [
                    'validation_rules' => [
                        'key' => 'required|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
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
                            'key' => 'required|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
                        ]
                    ],

                    'details' => [
                        'validation_rules' => [
                            'key' => 'required|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
                        ]
                    ],

                    'passthru' => [
                        'validation_rules' => [
                            'key' => 'required|string|exists:' . config('crmomni-migration.table_name.organizations') . ',hash|max:36',
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