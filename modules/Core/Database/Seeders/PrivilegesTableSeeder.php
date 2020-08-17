<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;

class PrivilegesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $privileges = [
            [
                'key' => 'list_all_organizations',
                'display_value' => 'Show All Organizations',
                'is_secure' => true
            ],
            [
                'key' => 'read_organization_data',
                'display_value' => 'Read/View Organization Data',
            ],
            [
                'key' => 'add_organization_data',
                'display_value' => 'Add Organization Data',
                'is_secure' => true
            ],
            [
                'key' => 'edit_organization_data',
                'display_value' => 'Edit/Amend Organization Data',
            ],
            
            [
                'key' => 'list_all_organization_accounts',
                'display_value' => 'Show All Accounts in the Organization',
            ],
            [
                'key' => 'add_new_account_data',
                'display_value' => 'Add Account Data',
            ],
            [
                'key' => 'edit_account_data',
                'display_value' => 'Edit/Manage Account Data',
            ],
            [
                'key' => 'delete_account_data',
                'display_value' => 'Delete Account Data',
            ],

            [
                'key' => 'list_all_organization_customers',
                'display_value' => 'Show Customers in the Organization',
            ],
            [
                'key' => 'add_new_customer_data',
                'display_value' => 'Add Customer Record',
            ],
            [
                'key' => 'edit_customer_data',
                'display_value' => 'Edit/Amend Customer Record',
            ],
            [
                'key' => 'delete_customer_data',
                'display_value' => 'Delete Customer Record',
            ],
            [
                'key' => 'show_customer_unmasked_data',
                'display_value' => 'Show Customer Record (Unmasked)',
            ],
            [
                'key' => 'show_customer_masked_data',
                'display_value' => 'Show Customer Record (Masked)',
            ],

            [ //add_new_note
                'key' => 'add_new_note',
                'display_value' => 'Add Note',
            ],
            [ //delete_note
                'key' => 'delete_note',
                'display_value' => 'Delete Note',
            ],

            [ //add_new_document
                'key' => 'add_new_document',
                'display_value' => 'Add Document',
            ],
            [ //delete_document
                'key' => 'delete_document',
                'display_value' => 'Delete Document',
            ],

            //Tele-Communication Privileges
            [
                'key' => 'allow_call_outgoing',
                'display_value' => 'Outgoing Call',
            ],
            [
                'key' => 'allow_call_incoming',
                'display_value' => 'Incoming Call',
            ],
            [
                'key' => 'allow_sms_outgoing',
                'display_value' => 'Send SMS',
            ],
            [
                'key' => 'allow_msg_outgoing',
                'display_value' => 'Send Messages',
            ],
            [
                'key' => 'allow_email_outgoing',
                'display_value' => 'Send E-Mail',
            ],
        ];

        foreach ($privileges as $privilege) {
            $response = factory(\Modules\Core\Models\Privilege\Privilege::class)->create([
                'key' => $privilege['key'],
                'display_value' => $privilege['display_value'],
                'is_secure' => isset($privilege['is_secure'])?$privilege['is_secure']:false,
            ]);
        } //Loop ends
    } //Function ends
} //Class ends
