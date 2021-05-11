<?php

namespace Modules\ServiceRequest\Database\Seeders;

use Modules\ServiceRequest\Models\ServiceRequest;
use Modules\Core\Models\Lookup\LookupValue;

use Illuminate\Database\Seeder;

class TaskDataSeeder extends Seeder
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
            echo('Executing: TaskDataSeeder ->');

            //Faker instance
            $faker = \Faker\Factory::create();

            $servicerequests = ServiceRequest::get();
            foreach ($servicerequests as $servicerequest) {
                //Organization
                $organization = $servicerequest->organization;

                //Get Lookup Value for Task
                $lookupvalueTaskId = 69;
                $lookupvalueTask = LookupValue::where('key', 'service_request_activity_type_task')
                    ->where(function ($innerQuery) use ($servicerequest) {
                        $innerQuery->where('org_id', $servicerequest['org_id'])
                            ->orWhere('org_id', 0);
                    })
                    ->first();

                $lookupvalueTaskId = (empty($lookupvalueTask))?$lookupvalueTask['id']:69;

                //Records of Account to be created
                $maxRecord = $faker->numberBetween(1, 4);

                //Create account & other objects
                for ($i=0; $i < $maxRecord; $i++) { 
                    $account = factory(\Modules\ServiceRequest\Models\Task::class)->create([
                        'org_id' => $servicerequest['org_id'],
                        'servicerequest_id' => $servicerequest['id'],
                        'type_id' => $lookupvalueTaskId,
                        'subtype_id' => $faker->numberBetween(71, 74),
                        'user_id' => 1,
                        'priority_id' => $faker->numberBetween(75, 77),
                    ]);
                } //Loop ends
               
            } //Loop ends
        } //End if
    } //function ends
}
