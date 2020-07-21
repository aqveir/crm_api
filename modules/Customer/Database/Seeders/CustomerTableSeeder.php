<?php

namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Seeder;

class CustomerTableSeeder extends Seeder
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

            //Create customer & other objects
            factory(\Modules\Customer\Models\Customer\Customer::class, 100)->create()
                ->each(function($customer) use ($faker) {

                    //Add Customer Details
                    for ($i=1; $i < $faker->numberBetween(2, 4); $i++) {
                        $typeId=0;
                        $subtypeId=0;
                        $countryId=0;
                        $identifier='';
                    
                        switch ($i) {
                            case 1: //Email
                                $typeId=26;
                                $subtypeId=$faker->numberBetween(30, 31);
                                $countryId=null;
                                $identifier=$faker->unique()->safeEmail();
                                break;
                    
                            case 2: //Phone
                                $typeId=27;
                                $subtypeId=$faker->numberBetween(32, 33);
                                $countryId=1;
                                $identifier=$faker->unique()->numberBetween(9800000000, 9900000000);
                                break;
                    
                            case 3: //Social Handle
                                $typeId=28;
                                $subtypeId=$faker->numberBetween(34, 36);
                                $countryId=null;
                                $identifier=$faker->url();
                                break;
                            
                            default:
                                $countryId=null;
                                break;
                        } //Switch ends

                        $customer->details()->save(factory(\Modules\Customer\Models\Customer\CustomerDetail::class)
                            ->make([
                                'org_id' => $customer['org_id'],
                                'customer_id' => $customer['id'],
                                'type_id' => $typeId,
                                'subtype_id' => $subtypeId,
                                'country_id' => $countryId,
                                'identifier' => $identifier,
                            ])
                        );
                    } //Loop ends

                    //Add Customer Address
                    for ($i=0; $i < $faker->numberBetween(1, 3); $i++) { 
                        $customer->addresses()->save(factory(\Modules\Customer\Models\Customer\CustomerAddress::class)
                            ->make([
                                'org_id' => $customer['org_id'],
                                'customer_id' => $customer['id'],
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
            'password' => $faker->password(),
            'first_name' => $faker->firstName(),
            'middle_name' => null,
            'last_name' => $faker->lastName(),
            'date_of_birth_at' => null,
    
            //Customer Relationship Keys
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

    private function dataCustomer($faker, int $counter=0) {
        return [
            'org_id' => 1,
            //'username' => (($counter%3)===1)?$faker->unique()->safeEmail():($faker->unique()->numberBetween(9800000000, 9900000000)),
            'password' => $faker->password(),
            'first_name' => $faker->firstName(),
            'middle_name' => null,
            'last_name' => $faker->lastName(),
            'date_of_birth_at' => null,
    
            //Customer Relationship Keys
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

    private function dataCustomerAddress($faker, $customer) {
        return [
            'org_id' => 1,
            'customer_id' => $customer['id'],
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

    private function dataCustomerDetail($faker, $customer, $index=1) {
        $typeId=0;
        $subtypeId=0;
        $countryId=0;
        $identifier='';
        switch ($index) {
            case 1: //Email
                $typeId=36;
                $subtypeId=$faker->numberBetween(40, 41);
                $countryId=null;
                $identifier=$faker->unique()->safeEmail();
                break;

            case 2: //Phone
                $typeId=37;
                $subtypeId=$faker->numberBetween(42, 43);
                $countryId=1;
                $identifier=$faker->unique()->numberBetween(9800000000, 9900000000);
                break;

            case 3: //Social Handle
                $typeId=38;
                $subtypeId=$faker->numberBetween(44, 46);
                $countryId=null;
                $identifier=$faker->url();
                break;
            
            default:
                $countryId=null;
                break;
        } //Switch ends

        return [
            'org_id' => 1,
            'customer_id' => $customer['id'],
            'type_id' => $typeId,
            'subtype_id' => $subtypeId,
            'country_id' => $countryId,
            'identifier' => $identifier,
            'proxy' => $faker->numberBetween(100000, 999999),
            'is_primary' => $faker->numberBetween(0, 1),
            'is_verified' => $faker->numberBetween(0, 1),
        ];
    } //Function ends

} //Class ends
