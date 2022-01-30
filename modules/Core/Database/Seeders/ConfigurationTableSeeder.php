<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;

class ConfigurationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $configurations = $this->dataConfigurations();

        foreach ($configurations as $configuration) {
            $response = factory(\Modules\Core\Models\Common\Configuration::class)->create($configuration);
        } //Loop ends
    }

    private function dataConfigurations()
    {
        return  [
            [ //Email - SMTP Configuration
                'type_id' => '12',
                'key' => 'configuration_mail_smtp',
                'display_value' => 'Mail SMTP Configuration',
                'schema' => json_encode(
                    [
                        'mail_host' => 'smtp.mailtrap.io',
                        'mail_port' => 2525,
                        'mail_username' => 'username',
                        'mail_password' => 'password',
                        'mail_encrypt' => null,
                        'mail_from_address' => 'john@doe.com',
                        'mail_from_name' => 'John Doe'
                    ]
                )
            ],
            [ //Telephony - Call Providers Selection
                'type_id' => '9',
                'key' => 'configuration_telephony_call_providers',
                'display_value' => 'Telephony Providers',
                'schema' => json_encode(
                    [
                        [
                            'provider_key' => 'configuration_telephony_providers_none',
                            'display_value' => 'None'
                        ],
                        [
                            'provider_key' => 'configuration_telephony_providers_exotel',
                            'display_value' => 'Exotel India'
                        ],
                        [
                            'provider_key' => 'configuration_telephony_providers_knowlarity',
                            'display_value' => 'Knowlarity India'
                        ],
                        [
                            'provider_key' => 'configuration_telephony_providers_servetel',
                            'display_value' => 'Servetel India'
                        ],                        
                        [
                            'provider_key' => 'configuration_telephony_providers_twilio',
                            'display_value' => 'Twilio'
                        ]
                    ]
                )
            ],
            [ //Telephony - SMS Providers Selection
                'type_id' => '9',
                'key' => 'configuration_telephony_sms_providers',
                'display_value' => 'Telephony Providers',
                'schema' => json_encode(
                    [
                        [
                            'provider_key' => 'configuration_telephony_providers_none',
                            'display_value' => 'None'
                        ],
                        [
                            'provider_key' => 'configuration_telephony_providers_exotel',
                            'display_value' => 'Exotel'
                        ],
                        [
                            'provider_key' => 'configuration_telephony_providers_twilio',
                            'display_value' => 'Twilio'
                        ],
                        [
                            'provider_key' => 'configuration_telephony_providers_indiasms',
                            'display_value' => 'India SMS'
                        ],
                        [
                            'provider_key' => 'configuration_telephony_providers_2',
                            'display_value' => 'Provider 2'
                        ],
                    ]
                )
            ],
            [ //Telephony - Outgoing Phone Number
                'type_id' => '9',
                'key' => 'configuration_telephony_outgoing_phone_number',
                'display_value' => 'Telephony - Outgoing Phone Number',
                'schema' => '987654321'
            ],
            [ //Telephony - Outgoing SMS Number
                'type_id' => '9',
                'key' => 'configuration_telephony_outgoing_sms_number',
                'display_value' => 'Telephony - Outgoing SMS Number',
                'schema' => '987654321'
            ],
            [ //DB Connection - Configuration
                'type_id' => '12',
                'key' => 'configuration_organiation_dbconn',
                'display_value' => 'Database Connection',
                'schema' => json_encode(
                    [
                        'driver' => 'mysql',
                        'url' => '',
                        'host' => 'localhost',
                        'port' => '3306',
                        'database' => '',
                        'username' => 'root',
                        'password' => 'root',
                        'unix_socket' => '',
                        'charset' => 'utf8mb4',
                        'collation' => 'utf8mb4_unicode_ci',
                        'prefix' => '',
                        'prefix_indexes' => true,
                        'strict' => true,
                        'engine' => null,
                        'options' => []
                    ]
                )
            ],
            [ //Telephony - Exotel Configuration
                'type_id' => '12',
                'key' => 'configuration_telephony_exotel',
                'display_value' => 'Telephony - Exotel Configuration',
                'filter' => 'configuration_telephony_providers_exotel',
                'schema' => json_encode(
                    [
                        'exotel_subdomain' => 'exotel_subdomain',
                        'exotel_sid' => 'enter_exotel_sid',
                        'exotel_api_key' => 'enter_exotel_api_key',
                        'exotel_api_token' => 'enter_exotel_api_token'
                    ]
                )
            ],
            [ //Telephony - Twilio Configuration
                'type_id' => '12',
                'key' => 'configuration_telephony_twilio',
                'display_value' => 'Telephony - Twilio Configuration',
                'filter' => 'configuration_telephony_providers_twilio',
                'schema' => json_encode(
                    [
                        'twilio_base_url' => 'enter_twilio_base_url',
                        'twilio_sid' => 'enter_twilio_sid',
                        'twilio_api_key' => 'enter_twilio_api_key',
                        'twilio_auth_token' => 'enter_twilio_auth_token'
                    ]
                )
            ],
            [ //Telephony - IndiaSMS Configuration
                'type_id' => '12',
                'key' => 'configuration_telephony_indiasms',
                'filter' => 'configuration_telephony_providers_indiasms',
                'display_value' => 'Telephony - IndiaSMS Configuration',
                'schema' => json_encode(
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
        ];
    }
}
