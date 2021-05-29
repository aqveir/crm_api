<?php

namespace Modules\ServiceRequest\Database\Seeders;

use Log;
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

                //Owner-Assignee
                $assigneeId = $servicerequest->owner['id'];

                //Org Users
                $users = $organization->users;
                if (!empty($users)) {
                    $randId = rand(0, (count($users)-1));

                    //Owner-Assignee
                    $assigneeId = $users[$randId]['id'];
                } //End if                

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
                        'created_by' => $servicerequest->owner['id']
                    ]);

                    //Add Participant (Owner)
                    $account->assignee()->save(factory(\Modules\ServiceRequest\Models\ActivityParticipant::class)
                        ->make([
                            'activity_id' => $account['id'],
                            'participant_type_id' => 90,
                            'participant_id' => $assigneeId
                        ])
                    );
                } //Loop ends
               
            } //Loop ends
        } //End if
    } //function ends
}
