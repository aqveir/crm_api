<?php

namespace Modules\ServiceRequest\Database\Seeders;

use Modules\ServiceRequest\Models\ServiceRequest;
use Modules\Core\Models\Lookup\LookupValue;

use Illuminate\Database\Seeder;

class ServiceRequestEventDataSeeder extends Seeder
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
            echo('Executing: ServiceRequestEventDataSeeder ->');

            //Faker instance
            $faker = \Faker\Factory::create();

            $servicerequests = ServiceRequest::get();
            foreach ($servicerequests as $servicerequest) {
                //Organization
                $organization = $servicerequest->organization;

                //Contact
                $contact = $servicerequest->contact;

                //Owner
                $ownerId=1;
                $owner = $servicerequest->owner;
                if (!empty($owner)) {
                    $ownerId = $owner['id'];
                } //End if

                //Get Lookup Value for Event
                $lookupvalueEventId = 70;
                $lookupvalueEvent = LookupValue::where('key', 'service_request_activity_type_event')
                    ->where(function ($innerQuery) use ($servicerequest) {
                        $innerQuery->where('org_id', $servicerequest['org_id'])
                            ->orWhere('org_id', 0);
                    })
                    ->first();

                $lookupvalueEventId = (empty($lookupvalueEvent))?$lookupvalueEvent['id']:70;

                //Records of Account to be created
                $maxRecord = $faker->numberBetween(1, 4);

                //Create account & other objects
                for ($i=0; $i < $maxRecord; $i++) { 
                    $event = factory(\Modules\ServiceRequest\Models\Event::class)->create([
                        'org_id' => $servicerequest['org_id'],
                        'servicerequest_id' => $servicerequest['id'],
                        'type_id' => $lookupvalueEventId,
                        'subtype_id' => 83,
                        'location' => $faker->streetAddress,
                        'created_by' => $ownerId,
                    ]);

                    //Add Participant (Owner)
                    $event->participants()->save(factory(\Modules\ServiceRequest\Models\ActivityParticipant::class)
                        ->make([
                            'activity_id' => $event['id'],
                            'participant_type_id' => 90,
                            'participant_id' => $ownerId
                        ])
                    );

                    //Add Participant (Contact)
                    $event->participants()->save(factory(\Modules\ServiceRequest\Models\ActivityParticipant::class)
                        ->make([
                            'activity_id' => $event['id'],
                            'participant_type_id' => 91,
                            'participant_id' => $contact['id']
                        ])
                    );
                } //Loop ends
               
            } //Loop ends
        } //End if
    }
}
