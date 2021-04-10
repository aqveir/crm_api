<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'org_id' => 0,
                'key' => 'super_admin',
                'display_value' => 'Super Administrator',
                'privileges' => [
                    //Manage Organization
                    'list_all_organizations', 'view_organization',
                    'add_organization', 'edit_organization',
                    'delete_organization',

                    //Manage Privileges
                    'list_all_privileges',

                    //Manage Roles
                    'list_all_roles', 'view_role', 
                    'add_role', 'edit_role', 'delete_role'
                ]
            ],
            [
                'org_id' => 1,
                'key' => 'organization_admin',
                'display_value' => 'Organization Administrator',
                'privileges' => [
                    //Manage Organization
                    'view_organization', 'edit_organization',

                    //Manage Privileges
                    'list_all_privileges',

                    //Manage Roles
                    'list_all_roles', 'view_role', 
                    'add_role', 'edit_role', 'delete_role',

                    //Manage Accounts
                    'list_all_organization_accounts', 'view_account',
                    'add_account', 'edit_account', 'delete_account',

                    //Manage Contact
                    'list_all_contacts', 'view_contact',
                    'add_contact', 'edit_contact', 'delete_contact', 
                    'show_contact_unmasked_data',

                    //Manage Notes
                    'add_note', 'delete_note',

                    //Manage Documents
                    'add_new_document', 'delete_document',
                    
                    //Manage Catalogue
                    'list_all_organization_catalogue',
                    'add_new_catalogue_data', 'edit_catalogue_data',
                ]
            ],
            [
                'org_id' => 1,
                'key' => 'account_admin',
                'display_value' => 'Account Admin',
                'privileges' => [
                    //Manage Accounts
                    'view_account', 'edit_account',

                    //Manage Contact
                    'list_account_contacts_only',

                    //Manage Notes
                    'add_note', 'delete_note',

                    //Manage Documents
                    'add_new_document', 'delete_document'
                ]
            ],
            [
                'org_id' => 1,
                'key' => 'telecaller_support',
                'display_value' => 'Support Telecallers',
                'privileges' => [
                    //Manage Contact
                    'list_user_contacts_only',

                    //Manage Notes
                    'add_note',

                    //Manage Documents
                    'add_new_document',
                ]
            ],
        ];

        foreach ($roles as $role) {
            $response = factory(\Modules\Core\Models\Role\Role::class)->create([
                'org_id' => $role['org_id'],
                'key' => $role['key'],
                'display_value' => $role['display_value'],
            ]);

            if($role['privileges'] && count($role['privileges'])>0) {
                foreach ($role['privileges'] as $keyPrivilege) {
                    $privilege =  \Modules\Core\Models\Privilege\Privilege::where('key', $keyPrivilege)->first();

                    if (!empty($privilege)) {
                        $join = \Modules\Core\Models\Role\RolePrivilege::create([
                            'role_id' => $response['id'],
                            'privilege_id' => $privilege['id'],
                            'is_active' => 1
                        ]);
                    } //End if
                } //Loop ends
            } //End if
        } //Loop ends
    } //Function ends
} //Class ends
