<?php

namespace Modules\ServiceRequest\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ServiceRequestDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call([            
            ServiceRequestDataSeeder::class,
            TaskDataSeeder::class,
            ServiceRequestEventDataSeeder::class
        ]);
    }
}
