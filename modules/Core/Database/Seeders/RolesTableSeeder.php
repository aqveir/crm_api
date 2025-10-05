<?php

namespace Modules\Core\Database\Seeders;

use Log;
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
                'is_secure' => true,
                'privileges' => [
                    //Manage Organization
                    'list_all_organizations', 'view_organization',
                    'add_organization', 'edit_organization',
                    'delete_organization',

                    //Manage Privileges
                    'list_all_privileges',

                    //Manage Roles
                    'list_all_roles', 'view_role', 
                    'add_role', 'edit_role', 'delete_role',

                    //Manage Preferences
                    'list_all_organization_preferences', 'view_preference',
                    'add_preference', 'edit_preference', 'delete_preference',

                    //Manage Subscriptions
                    'manage_subscriptions', 
                    'add_subscription', 'edit_subscription', 'delete_subscription',

                    //Manage Accounts
                    'list_all_organization_accounts', 'view_account',
                    'add_account', 'edit_account', 'delete_account'
                ]
            ],
            array_merge(['org_id' => 1], config('core.settings.new_organization.default_roles')[0]),
            array_merge(['org_id' => 1], config('core.settings.new_organization.default_roles')[1]),
            array_merge(['org_id' => 1], config('core.settings.new_organization.default_roles')[2]),
            array_merge(['org_id' => 1], config('core.settings.new_organization.default_roles')[3])
        ];

        foreach ($roles as $role) {
            $response = factory(\Modules\Core\Models\Role\Role::class)->create([
                'org_id' => $role['org_id'],
                'key' => $role['key'],
                'display_value' => $role['display_value'],
                'is_secure' => $role['is_secure']
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
