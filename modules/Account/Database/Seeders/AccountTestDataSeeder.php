<?php

namespace Modules\Account\Database\Seeders;

use Modules\Core\Models\Organization\Organization;
use Illuminate\Database\Seeder;

class AccountTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Log environment
        echo('Environment -> ' . \App::environment());

        //Environemnt check
        if (\App::environment() !== 'production') {

            //Faker instance
            $faker = \Faker\Factory::create();

            $organizations = Organization::get();
            foreach ($organizations as $organization) {

                //Records of Account to be created
                $maxRecord = $faker->numberBetween(1, 4);

                //Create account & other objects
                for ($i=0; $i < $maxRecord; $i++) { 
                    $account = factory(\Modules\Account\Models\Account::class)->create([
                        'org_id' => $organization['id'],
                        'is_default' => ($i==0)
                    ]);
                } //Loop ends
               
            } //Loop ends
        } //End if
    }
}
