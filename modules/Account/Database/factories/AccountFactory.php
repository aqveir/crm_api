<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Modules\Account\Models\Account;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Account::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'description' => $faker->sentence($nbWords = 6, $variableNbWords = true),

        'type_id' => $faker->numberBetween(28, 30),
        'address' => $faker->address,
        'locality' => $faker->streetName,
        'city' => $faker->city,

        'website' => 'http://' . $faker->domainName,
        'email' => $faker->email,
        'phone' => $faker->phoneNumber
    ];
});
