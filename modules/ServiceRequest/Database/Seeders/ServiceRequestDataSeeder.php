<?php

namespace Modules\ServiceRequest\Database\Seeders;

use Modules\Contact\Models\Contact\Contact;
use Illuminate\Database\Seeder;

class ServiceRequestDataSeeder extends Seeder
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

            //Log environment
            echo('Executing: ServiceRequestDataSeeder ->');

            //Faker instance
            $faker = \Faker\Factory::create();

            //Get Contacts
            $contacts = Contact::get();
            foreach ($contacts as $contact) {

                //Organization
                $organization = $contact->organization;

                //Organization-Account
                $accounts = $organization->accounts;
                $dataAccounts = array_column($accounts->toArray(), 'id');

                //Default Org Owner
                $ownerId=1;
                $users = $organization->users;
                if ((!empty($users)) && (is_array($users)) && (count($users)>0)) {
                    $dataUsers = array_column($users->toArray(), 'id');
                    $ownerId = $users[$faker->numberBetween(0, (count($dataUsers)-1))]['id'];
                } //End if

                //Records of SequestRequest to be created
                $maxRecord = $faker->numberBetween(0, 4);

                if ($maxRecord>0) {
                    //Create account & other objects
                    for ($i=0; $i < $maxRecord; $i++) { 
                        $account = factory(\Modules\ServiceRequest\Models\ServiceRequest::class)->create([
                            'org_id' => $organization['id'],
                            'contact_id' => $contact['id'],
                            'account_id' => $accounts[$faker->numberBetween(0, (count($dataAccounts)-1))]['id'],
                            'type_id' => $faker->numberBetween(28, 30),
                            'owner_id' => $ownerId,
                            'category_id' => $faker->numberBetween(58, 60),
                            'status_id' => $faker->numberBetween(62, 66),
                            'stage_id' => $faker->numberBetween(67, 68),
                        ]);
                    } //Loop ends
                } //End if

            } //Loop ends
        } //End if
    }
}
