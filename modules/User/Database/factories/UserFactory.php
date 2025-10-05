<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Modules\User\Models\User\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

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

$factory->define(User::class, function (Faker $faker) {
    $unqEmail = $faker->unique()->safeEmail;

    return [
        'org_id' => $faker->numberBetween(1, 30),
        'username' => $unqEmail,
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'email' => $unqEmail,
        'first_name' => $faker->firstName($gender = null),
        'last_name' => $faker->lastName,
        'remember_token' => $faker->sha256,
        'is_verified' => $faker->boolean
    ];
});
