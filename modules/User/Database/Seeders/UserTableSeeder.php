<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = factory(\Modules\User\Models\User\User::class)->create([
            'org_id' => 1,
            'username' => 'admin@ellaisys.com',
            'password' => 'password',
            'email' => 'admin@ellaisys.com',
            'first_name' => 'EllaiSys',
            'last_name' => 'Admin',
            'is_verified' => true
        ]);

        $data = \Modules\User\Models\User\UserRole::create([
            'user_id' => $user['id'],
            'role_id' => 1,
            'description' => 'System Generated'
        ]);
    }
}
