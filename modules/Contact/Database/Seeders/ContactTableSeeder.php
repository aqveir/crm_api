<?php

namespace Modules\Contact\Database\Seeders;

use Illuminate\Database\Seeder;

class ContactTableSeeder extends Seeder
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
            $faker->addProvider(new \Faker\Provider\en_US\PhoneNumber($faker));

            //Create contact & other objects
            factory(\Modules\Contact\Models\Contact\Contact::class, 100)->create()
                ->each(function($contact) use ($faker) {

                    //Add Contact Details
                    for ($i=1; $i < $faker->numberBetween(2, 4); $i++) {
                        $typeId=0;
                        $subtypeId=0;
                        $countryId=0;
                        $identifier='';
                    
                        switch ($i) {
                            case 1: //Email
                                $typeId=42;
                                $subtypeId=$faker->numberBetween(46, 47);
                                $countryId=null;
                                $identifier=$faker->unique()->safeEmail();
                                break;
                    
                            case 2: //Phone
                                $typeId=43;
                                $subtypeId=$faker->numberBetween(48, 49);
                                $identifier=$faker->e164PhoneNumber();
                                break;
                    
                            case 3: //Social Handle
                                $typeId=44;
                                $subtypeId=$faker->numberBetween(50, 54);
                                $countryId=null;
                                $identifier=$faker->url();
                                break;
                            
                            default:
                                $countryId=null;
                                break;
                        } //Switch ends

                        $contact->details()->save(factory(\Modules\Contact\Models\Contact\ContactDetail::class)
                            ->make([
                                'org_id' => $contact['org_id'],
                                'contact_id' => $contact['id'],
                                'type_id' => $typeId,
                                'subtype_id' => $subtypeId,
                                'identifier' => $identifier,
                            ])
                        );
                    } //Loop ends

                    //Add Contact Address
                    for ($i=0; $i < $faker->numberBetween(1, 3); $i++) { 
                        $contact->addresses()->save(factory(\Modules\Contact\Models\Contact\ContactAddress::class)
                            ->make([
                                'org_id' => $contact['org_id'],
                                'contact_id' => $contact['id'],
                            ])
                        );
                    } //Loop ends
                });            

        } //Environment check ends

    } //Function ends

    private function dataCompany($faker, int $counter=0) {
        return [
            'org_id' => 1,
            //'username' => (($counter%3)===1)?$faker->safeEmail():(9851000000+$faker->randomNumber(6,true)),
            //'password' => $faker->password(),
            'first_name' => $faker->firstName(),
            'middle_name' => null,
            'last_name' => $faker->lastName(),
            'birth_at' => null,
    
            //Contact Relationship Keys
            'type_id' => 1,
            'status_id' => 1,
            'gender_id' => 1,
            'group_id' => 1,
            'company_id' => 1,
    
            'is_verified' => 1,
    
            //Referral Fields
            'referral_code' => Str::random(10),
            'referred_by' => 0,
        ];
    } //Function ends

    private function dataContact($faker, int $counter=0) {
        return [
            'org_id' => 1,
            //'username' => (($counter%3)===1)?$faker->unique()->safeEmail():($faker->unique()->numberBetween(9800000000, 9900000000)),
            //'password' => $faker->password(),
            'first_name' => $faker->firstName(),
            'middle_name' => null,
            'last_name' => $faker->lastName(),
            'birth_at' => null,
    
            //Contact Relationship Keys
            'type_id' => 1,
            'status_id' => 1,
            'gender_id' => 1,
            'group_id' => 1,
            'occupation_id' => 1,
    
            'is_verified' => 1,
    
            //Referral Fields
            'referral_code' => Str::random(10),
            'referred_by' => 0,
        ];
    } //Function ends

    private function dataContactAddress($faker, $contact) {
        return [
            'org_id' => 1,
            'contact_id' => $contact['id'],
            'type_id' => 1,

            'name' => 'home',
            'apartment_id' => 1,
            'address1' => $faker->streetAddress(),
            'address2' => null,
            'locality' => $faker->lastName(),
            'city' => $faker->city(),
            'state_id' => 1,
            'country_id' => 1,
            'zipcode' => $faker->postcode(),

            'longitude' => $faker->longitude(),
            'latitude' => $faker->latitude(),
        ];
    } //Function ends

    private function dataContactDetail($faker, $contact, $index=1) {
        $typeId=0;
        $subtypeId=0;
        $countryId=0;
        $identifier='';
        switch ($index) {
            case 1: //Email
                $typeId=42;
                $subtypeId=$faker->numberBetween(46, 47);
                $identifier=$faker->unique()->safeEmail();
                break;

            case 2: //Phone
                $typeId=43;
                $subtypeId=$faker->numberBetween(48, 49);
                $identifier=$faker->e164PhoneNumber();
                break;

            case 3: //Social Handle
                $typeId=44;
                $subtypeId=$faker->numberBetween(50, 54);
                $identifier=$faker->url();
                break;
            
            default:
                break;
        } //Switch ends

        return [
            'org_id' => 1,
            'contact_id' => $contact['id'],
            'type_id' => $typeId,
            'subtype_id' => $subtypeId,
            'identifier' => $identifier,
            'proxy' => $faker->numberBetween(100000, 999999),
            'is_primary' => $faker->numberBetween(0, 1),
            'is_verified' => $faker->numberBetween(0, 1),
        ];
    } //Function ends

} //Class ends
