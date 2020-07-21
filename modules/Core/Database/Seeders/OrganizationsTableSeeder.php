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
            $response = factory(\Modules\Core\Models\Organization\Organization::class)->create();

            //Save configurations
            if (!empty($organization['configurations'])) 
            {
                $response->configurations()->attach($organization['configurations']);
            } //End if            
        } //Loop ends
    }

    private function dataOrganizations()
    {
        return  [
            [
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
                                'mail_from_address' => 'support@ecomni.com',
                                'mail_from_name' => 'Ecomni Mailbox'
                            ]
                        )
                    ],
                    [
                        'configuration_id' => 2, 
                        'value' => json_encode(
                            [
                                'exotel_sid' => 'omnichannel',
                                'exotel_token' => 'hfisdf78yfhasdufsd8yfhs'
                            ]
                        )
                    ],
                    [
                        'configuration_id' => 3, 
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
                        'value' => '123456789'
                    ]
                ]
            ],
            [
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
                                'mail_from_address' => 'support@ecomni.com',
                                'mail_from_name' => 'Ecomni Mailbox'
                            ]
                        )
                    ]
                ]
            ],
            [ ],
        ];
    }
}
