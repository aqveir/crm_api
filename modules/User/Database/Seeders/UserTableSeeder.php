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
        //Default User
        $user = factory(\Modules\User\Models\User\User::class)->create([
            'org_id' => 1,
            'username' => 'admin@ellaisys.com',
            'password' => 'Test@1234',
            'email' => 'admin@ellaisys.com',
            'first_name' => 'EllaiSys',
            'last_name' => 'Admin',
            'is_verified' => true
        ]);

        $data = \Modules\User\Models\User\UserRole::create([
            'org_id' => $user->organization['id'],
            'user_id' => $user['id'],
            'role_id' => 1,
            'description' => 'System Generated'
        ]);

        //Log environment
        echo('Environment -> ' . \App::environment());

        //Environemnt check
        if (\App::environment() !== 'production') {

            //Default Remote user
            $remoteUser = factory(\Modules\User\Models\User\User::class)->create([
                'org_id' => 1,
                'username' => 'ellaisys_remote_user',
                'password' => 'ellaisys_remote_password',
                'first_name' => 'EllaiSys',
                'last_name' => 'Remote User',
                'is_verified' => true,
                'is_remote_access_only' => true
            ]);
            $data = \Modules\User\Models\User\UserRole::create([
                'org_id' => $remoteUser->organization['id'],
                'user_id' => $remoteUser['id'],
                'role_id' => 2,
                'description' => 'System Generated'
            ]);

            //Faker instance
            $faker = \Faker\Factory::create();

            factory(\Modules\User\Models\User\User::class, 200)->create()
            ->each(function($user) use ($faker) {

                $data = \Modules\User\Models\User\UserRole::create([
                    'org_id' => $user->organization['id'],
                    'user_id' => $user['id'],
                    'role_id' => $faker->numberBetween(2, 4),
                    'description' => 'System Generated'
                ]);
            });

            //Localhost User
            $localuser = factory(\Modules\User\Models\User\User::class)->create([
                'org_id' => 5,
                'username' => 'localhost@vomoto.com',
                'password' => 'password',
                'email' => 'localhost@vomoto.com',
                'first_name' => 'EllaiSys Localhost',
                'last_name' => 'Admin',
                'is_verified' => true
            ]);

            $data = \Modules\User\Models\User\UserRole::create([
                'org_id' => 5,
                'user_id' => $localuser['id'],
                'role_id' => 1,
                'description' => 'System Generated'
            ]);
            $data = \Modules\User\Models\User\UserRole::create([
                'org_id' => 5,
                'user_id' => $localuser['id'],
                'role_id' => 2,
                'description' => 'System Generated'
            ]);

        } //End if
    }
}
