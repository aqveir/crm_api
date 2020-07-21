<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class CoreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Model::unguard();

        $this->call([
            CountryTableSeeder::class,
            CurrencyTableSeeder::class,
            TimezoneTableSeeder::class,
            ConfigurationTableSeeder::class,

            LookupTableSeeder::class,

            OrganizationsTableSeeder::class,
            PrivilegesTableSeeder::class,
            RolesTableSeeder::class,
        ]);
    }
}
