<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Modules\Contact\Models\Contact\Contact;
use Modules\Contact\Models\Contact\ContactDetail;
use Modules\Contact\Models\Contact\ContactAddress;

use Faker\Generator as Faker;

$factory->define(Contact::class, function (Faker $faker) {
    return [
        'org_id' => $faker->numberBetween(1, 3),
        //'username' => (($counter%3)===1)?$faker->safeEmail():(9851000000+$faker->randomNumber(6,true)),
        //'password' => '12345678',
        'first_name' => $faker->firstName(),
        'middle_name' => null,
        'last_name' => $faker->lastName(),
        'birth_at' => null,

        //Contact Relationship Keys
        'type_id' => 1,
        'gender_id' => 1,
        'group_id' => 1,

        'is_verified' => $faker->numberBetween(0, 1),

        //Referral Fields
        'referral_code' => Str::random(10),
        'referred_by' => 0,
    ];
});

$factory->define(ContactDetail::class, function (Faker $faker) {
    return [
        'org_id' => 1,
        'contact_id' => 0,
        'type_id' => 0,
        'subtype_id' => 0,
        'identifier' => 0,
        'proxy' => $faker->numberBetween(100000, 999999),
        'is_primary' => $faker->numberBetween(0, 1),
        'is_verified' => $faker->numberBetween(0, 1),
    ];
});

$factory->define(ContactAddress::class, function (Faker $faker) {
    return [
        'org_id' => 1,
        'contact_id' => 0,
        'type_id' => 1,

        'name' => 'home',
        'apartment_id' => null,
        'address1' => $faker->streetAddress(),
        'address2' => null,
        'locality' => $faker->lastName(),
        'city' => $faker->city(),
        'state' => $faker->state(),
        'country_id' => 1,
        'zipcode' => $faker->postcode(),

        'longitude' => $faker->longitude(),
        'latitude' => $faker->latitude(),
    ];
});
