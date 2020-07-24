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
                'type_id' => '1',
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
            [ //Telephony - Exotel Configuration
                'type_id' => '1',
                'key' => 'configuration_telephony_exotel',
                'display_value' => 'Telephony - Exotel Configuration',
                'schema' => json_encode(
                    [
                        'exotel_sid' => 'omnichannel',
                        'exotel_token' => 'hfisdf78yfhasdufsd8yfhs'
                    ]
                )
            ],
            [ //Telephony - IndiaSMS Configuration
                'type_id' => '1',
                'key' => 'configuration_telephony_indiasms',
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
            [ //Telephony - Outgoing Phone Number
                'type_id' => '1',
                'key' => 'configuration_telephony_outgoing_phone_number',
                'display_value' => 'Telephony - Outgoing Phone Number',
                'schema' => '987654321'
            ],
            [ //Telephony - Outgoing SMS Number
                'type_id' => '1',
                'key' => 'configuration_telephony_outgoing_sms_number',
                'display_value' => 'Telephony - Outgoing SMS Number',
                'schema' => '987654321'
            ],
        ];
    }
}