<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;

class OrganizationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $organizations = $this->dataOrganizations();

        foreach ($organizations as $organization) {
            //Industry object
            $industry = \Modules\Core\Models\Lookup\LookupValue::where('key', $organization['industry'])->first();

            $response = factory(\Modules\Core\Models\Organization\Organization::class)->create([
                'name' => $organization['name'],
                'subdomain' => $organization['subdomain'],
                'industry_id' => $industry['id'],
            ]);
            
            //Save configurations
            if (!empty($organization['configurations'])) 
            {
                $response->configurations()->attach($organization['configurations']);
            } //End if
        } //Loop ends

        //Environemnt check
        if (\App::environment() !== 'production') { 
            factory(\Modules\Core\Models\Organization\Organization::class, 30)->create();
        } //End if
    }

    private function dataOrganizations()
    {
        return  [
            [
                'name' => 'EllaiSys',
                'subdomain' => 'ellaisys',
                'industry' => 'industry_type_travel',
                'configurations' => [
                    [
                        'configuration_id' => 1, 
                        'value' => json_encode(
                            [
                                'mail_host' => 'smtp.mailtrap.io',
                                'mail_port' => 2525,
                                'mail_username' => '23e3c5cd6c3c51',
                                'mail_password' => '4d519ca3c4e4c9',
                                'mail_encrypt' => null,
                                'mail_from_address' => 'support@ellaisys.com',
                                'mail_from_name' => 'EllaiSys CRM Omni Account'
                            ]
                        )
                    ],
                    [
                        'configuration_id' => 2, 
                        'value' => 'configuration_telephony_providers_exotel' 
                    ],
                    [
                        'configuration_id' => 7, 
                        'value' => json_encode(
                            [
                                'exotel_subdomain' => '@api.exotel.com',
                                'exotel_sid' => 'ellaisys1',
                                'exotel_api_key' => '6ec3bbe2fc1ca0f0d9fba19f2a4007a75847e2323538a895',
                                'exotel_api_token' => '1d9ccab757bc93a3ce4583adf539bf2c6ea6beb3c3ef62c8'
                            ]
                        )
                    ],
                    [
                        'configuration_id' => 9, 
                        'value' => json_encode(
                            [
                                'api_endpoint' => 'https://app.indiasms.com/sendsms/bulksms.php',
                                'api_username' => 'api_username',
                                'api_password ' => 'api_password',
                                'api_verb' => 'GET',
                                'sms_type' => 'TEXT',
                                'sender' => 'your-6char-senderid',
                                'payload_signature' => [
                                    'username' => '[api_username]',
                                    'password' => '[api_password]',
                                    'type' => '[sms_type]',
                                    'sender' => '[sender]',
                                    'mobile' => '[!mobile_number!]',
                                    'message' => '[!sms_message!]'
                                ]
                            ]
                        )
                    ],
                    [
                        'configuration_id' => 4, 
                        'value' => '08047494247'
                    ]
                ]
            ],
            [
                'name' => 'Demo',
                'subdomain' => 'demo',
                'industry' => 'industry_type_vanilla',
                'configurations' => [
                    [
                        'configuration_id' => 1, 
                        'value' => json_encode(
                            [
                                'mail_host' => 'smtp.mailtrap.io',
                                'mail_port' => 2525,
                                'mail_username' => '23e3c5cd6c3c51',
                                'mail_password' => '4d519ca3c4e4c9',
                                'mail_encrypt' => null,
                                'mail_from_address' => 'support@demo.com',
                                'mail_from_name' => 'Demo CRM Omni Account'
                            ]
                        )
                    ],
                    [
                        'configuration_id' => 2, 
                        'value' => 'configuration_telephony_providers_exotel' 
                    ],
                    [
                        'configuration_id' => 8, 
                        'value' => json_encode(
                            [
                                'twilio_base_url' => 'default',
                                'twilio_sid' => 'AC48d21145a7be8b1cab763122ca2989d9',
                                'twilio_api_key' => 'portiqo',
                                'twilio_auth_token' => '2671ecec5d4e8e7f1fd16e059822281c'
                            ]
                        )
                    ],
                    [
                        'configuration_id' => 4, 
                        'value' => '08047494247'
                    ]
                ]
            ],
            [
                'name' => 'Portiqo',
                'subdomain' => 'portiqo',
                'industry' => 'industry_type_real_estate',
                'configurations' => [
                    [
                        'configuration_id' => 1, 
                        'value' => json_encode(
                            [
                                'mail_host' => 'smtp.mailtrap.io',
                                'mail_port' => 2525,
                                'mail_username' => '23e3c5cd6c3c51',
                                'mail_password' => '4d519ca3c4e4c9',
                                'mail_encrypt' => null,
                                'mail_from_address' => 'support@ellaisys.com',
                                'mail_from_name' => 'EllaiSys CRM Omni Account'
                            ]
                        )
                    ],
                    [
                        'configuration_id' => 2, 
                        'value' => 'configuration_telephony_providers_exotel' 
                    ],
                    [
                        'configuration_id' => 7, 
                        'value' => json_encode(
                            [
                                'exotel_subdomain' => '@api.exotel.com',
                                'exotel_sid' => 'portiqo',
                                'exotel_api_key' => 'portiqo',
                                'exotel_api_token' => '9d6555a64e9b1bbf94a3ba3fd3e87363d63d54a1'
                            ]
                        )
                    ],
                    [
                        'configuration_id' => 9, 
                        'value' => json_encode(
                            [
                                'api_endpoint' => 'https://app.indiasms.com/sendsms/bulksms.php',
                                'api_username' => 'api_username',
                                'api_password ' => 'api_password',
                                'api_verb' => 'GET',
                                'sms_type' => 'TEXT',
                                'sender' => 'your-6char-senderid',
                                'payload_signature' => [
                                    'username' => '[api_username]',
                                    'password' => '[api_password]',
                                    'type' => '[sms_type]',
                                    'sender' => '[sender]',
                                    'mobile' => '[!mobile_number!]',
                                    'message' => '[!sms_message!]'
                                ]
                            ]
                        )
                    ],
                    [
                        'configuration_id' => 4, 
                        'value' => '08047494247'
                    ]
                ]
            ],
            [
                'name' => 'Kerasi',
                'subdomain' => 'kesari',
                'industry' => 'industry_type_travel',
                'configurations' => [
                    [
                        'configuration_id' => 1, 
                        'value' => json_encode(
                            [
                                'mail_host' => 'smtp.mailtrap.io',
                                'mail_port' => 2525,
                                'mail_username' => '23e3c5cd6c3c51',
                                'mail_password' => '4d519ca3c4e4c9',
                                'mail_encrypt' => null,
                                'mail_from_address' => 'support@ellaisys.com',
                                'mail_from_name' => 'EllaiSys CRM Omni Account'
                            ]
                        )
                    ],
                    [
                        'configuration_id' => 2, 
                        'value' => 'configuration_telephony_providers_exotel' 
                    ],
                    [
                        'configuration_id' => 7, 
                        'value' => json_encode(
                            [
                                'exotel_subdomain' => '@api.exotel.com',
                                'exotel_sid' => 'kesari',
                                'exotel_api_key' => 'kesari',
                                'exotel_api_token' => '9d6555a64e9b1bbf94a3ba3fd3e87363d63d54a1'
                            ]
                        )
                    ],
                    [
                        'configuration_id' => 9, 
                        'value' => json_encode(
                            [
                                'api_endpoint' => 'https://app.indiasms.com/sendsms/bulksms.php',
                                'api_username' => 'api_username',
                                'api_password ' => 'api_password',
                                'api_verb' => 'GET',
                                'sms_type' => 'TEXT',
                                'sender' => 'your-6char-senderid',
                                'payload_signature' => [
                                    'username' => '[api_username]',
                                    'password' => '[api_password]',
                                    'type' => '[sms_type]',
                                    'sender' => '[sender]',
                                    'mobile' => '[!mobile_number!]',
                                    'message' => '[!sms_message!]'
                                ]
                            ]
                        )
                    ],
                    [
                        'configuration_id' => 4, 
                        'value' => '08047494247'
                    ]
                ]
            ],
            [
                'name' => 'Localhost',
                'subdomain' => 'localhost',
                'industry' => 'industry_type_vanilla',
                'configurations' => [
                    [
                        'configuration_id' => 1, 
                        'value' => json_encode(
                            [
                                'mail_host' => 'smtp.mailtrap.io',
                                'mail_port' => 2525,
                                'mail_username' => '23e3c5cd6c3c51',
                                'mail_password' => '4d519ca3c4e4c9',
                                'mail_encrypt' => null,
                                'mail_from_address' => 'support@ellaisys.com',
                                'mail_from_name' => 'EllaiSys CRM Omni Account'
                            ]
                        )
                    ],
                    [
                        'configuration_id' => 2, 
                        'value' => 'configuration_telephony_providers_exotel' 
                    ],
                    [
                        'configuration_id' => 7, 
                        'value' => json_encode(
                            [
                                'exotel_subdomain' => '@api.exotel.com',
                                'exotel_sid' => 'kesari',
                                'exotel_api_key' => 'kesari',
                                'exotel_api_token' => '9d6555a64e9b1bbf94a3ba3fd3e87363d63d54a1'
                            ]
                        )
                    ],
                    [
                        'configuration_id' => 9, 
                        'value' => json_encode(
                            [
                                'api_endpoint' => 'https://app.indiasms.com/sendsms/bulksms.php',
                                'api_username' => 'api_username',
                                'api_password ' => 'api_password',
                                'api_verb' => 'GET',
                                'sms_type' => 'TEXT',
                                'sender' => 'your-6char-senderid',
                                'payload_signature' => [
                                    'username' => '[api_username]',
                                    'password' => '[api_password]',
                                    'type' => '[sms_type]',
                                    'sender' => '[sender]',
                                    'mobile' => '[!mobile_number!]',
                                    'message' => '[!sms_message!]'
                                ]
                            ]
                        )
                    ],
                    [
                        'configuration_id' => 4, 
                        'value' => '08047494247'
                    ]
                ]
            ],
        ];
    }
}
