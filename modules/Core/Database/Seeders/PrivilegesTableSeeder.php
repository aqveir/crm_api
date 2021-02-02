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
            [ //list_all_organizations
                'key' => 'list_all_organizations',
                'display_value' => 'Show All Organizations',
                'is_secure' => true
            ],
            [ //read_organization_data
                'key' => 'read_organization_data',
                'display_value' => 'Read/View Organization Data',
            ],
            [ //add_organization
                'key' => 'add_organization',
                'display_value' => 'Add Organization',
                'is_secure' => true
            ],
            [ //edit_organization
                'key' => 'edit_organization',
                'display_value' => 'Edit/Amend Organization Data',
            ],
            [ //delete_organization
                'key' => 'delete_organization',
                'display_value' => 'Delete Organization',
                'is_secure' => true
            ],

            [ //list_all_roles
                'key' => 'list_all_roles',
                'display_value' => 'Show All Organization Roles',
            ],
            [ //read_role_data
                'key' => 'read_role_data',
                'display_value' => 'Read/View Role Data',
            ],
            [ //add_role
                'key' => 'add_role',
                'display_value' => 'Add Role',
            ],
            [ //edit_role
                'key' => 'edit_role',
                'display_value' => 'Edit/Amend Role Data',
            ],
            [ //delete_role
                'key' => 'delete_role',
                'display_value' => 'Delete Role',
            ],
            
            [ //list_all_organization_accounts
                'key' => 'list_all_organization_accounts',
                'display_value' => 'Show All Accounts in the Organization',
            ],
            [ //add_account
                'key' => 'add_account',
                'display_value' => 'Add Account Data',
            ],
            [ //edit_account
                'key' => 'edit_account',
                'display_value' => 'Edit/Manage Account Data',
            ],
            [ //delete_account
                'key' => 'delete_account',
                'display_value' => 'Delete Account Data',
            ],

            [ //list_all_organization_customers
                'key' => 'list_all_organization_customers',
                'display_value' => 'Show Customers in the Organization',
            ],
            [ //add_customer
                'key' => 'add_customer',
                'display_value' => 'Add Customer Record',
            ],
            [ //edit_customer
                'key' => 'edit_customer',
                'display_value' => 'Edit/Amend Customer Record',
            ],
            [ //delete_customer
                'key' => 'delete_customer',
                'display_value' => 'Delete Customer Record',
            ],
            [ //show_customer_unmasked_data
                'key' => 'show_customer_unmasked_data',
                'display_value' => 'Show Customer Record (Unmasked)',
            ],

            //Note Privileges
            [ //add_note
                'key' => 'add_note',
                'display_value' => 'Add Note',
            ],
            [ //edit_note
                'key' => 'edit_note',
                'display_value' => 'Amend/Edit Note',
            ],
            [ //delete_note
                'key' => 'delete_note',
                'display_value' => 'Delete Note',
            ],

            //Document Privileges
            [ //add_document
                'key' => 'add_document',
                'display_value' => 'Add Document',
            ],
            [ //edit_document
                'key' => 'edit_document',
                'display_value' => 'Amend/Edit Document',
            ],
            [ //delete_document
                'key' => 'delete_document',
                'display_value' => 'Delete Document',
            ],
            [ //view_document
                'key' => 'view_document',
                'display_value' => 'View/Download Document',
            ],          

            //Tele-Communication Privileges
            [ //allow_call_outgoing
                'key' => 'allow_call_outgoing',
                'display_value' => 'Outgoing Call',
            ],
            [ //allow_call_incoming
                'key' => 'allow_call_incoming',
                'display_value' => 'Incoming Call',
            ],
            [ //allow_sms_outgoing
                'key' => 'allow_sms_outgoing',
                'display_value' => 'Send SMS',
            ],
            [ //allow_msg_outgoing
                'key' => 'allow_msg_outgoing',
                'display_value' => 'Send Messages',
            ],
            [ //allow_email_outgoing
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
