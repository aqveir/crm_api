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
                    'list_all_organizations', 'read_organization_data',
                    'add_organization', 'edit_organization',
                    'delete_organization',

                    //Manage Roles
                    'list_all_roles'
                ]
            ],
            [
                'org_id' => 1,
                'key' => 'organization_admin',
                'display_value' => 'Organization Administrator',
                'privileges' => [
                    //Manage Organization
                    'list_all_organization_stores', 
                    
                    //Manage Store
                    'add_new_stores_data', 'edit_stores_data', 
                    
                    //Manage Catalogue
                    'list_all_organization_catalogue',
                    'add_new_catalogue_data', 'edit_catalogue_data',

                    //Manage Contact
                    'list_all_contacts', 'view_contact',
                    'add_contact', 'edit_contact', 'delete_contact', 
                    'show_contact_unmasked_data',

                    //Manage Notes
                    'add_note', 'delete_note',

                    //Manage Documents
                    'add_new_document', 'delete_document'
                ]
            ],
            [
                'org_id' => 1,
                'key' => 'account_admin',
                'display_value' => 'Account Admin',
                'privileges' => [
                    //Manage Store
                    'edit_stores_data',

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
