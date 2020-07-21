<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Modules\Customer\Models\Customer\Customer;
use Modules\Customer\Models\Customer\CustomerDetail;
use Modules\Customer\Models\Customer\CustomerAddress;

use Faker\Generator as Faker;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        'org_id' => $faker->numberBetween(1, 3),
        //'username' => (($counter%3)===1)?$faker->safeEmail():(9851000000+$faker->randomNumber(6,true)),
        'password' => '12345678',
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

        'is_verified' => $faker->numberBetween(0, 1),

        //Referral Fields
        'referral_code' => Str::random(10),
        'referred_by' => 0,
    ];
});

$factory->define(CustomerDetail::class, function (Faker $faker) {
    return [
        'org_id' => 1,
        'customer_id' => 0,
        'type_id' => 0,
        'subtype_id' => 0,
        'country_id' => 0,
        'identifier' => 0,
        'proxy' => $faker->numberBetween(100000, 999999),
        'is_primary' => $faker->numberBetween(0, 1),
        'is_verified' => $faker->numberBetween(0, 1),
    ];
});

$factory->define(CustomerAddress::class, function (Faker $faker) {
    return [
        'org_id' => 1,
        'customer_id' => 0,
        'type_id' => 1,

        'name' => 'home',
        'apartment_id' => null,
        'address1' => $faker->streetAddress(),
        'address2' => null,
        'locality' => $faker->lastName(),
        'city' => $faker->city(),
        'state_id' => null,
        'country_id' => 1,
        'zipcode' => $faker->postcode(),

        'longitude' => $faker->longitude(),
        'latitude' => $faker->latitude(),
    ];
});
