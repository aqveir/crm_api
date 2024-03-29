<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Modules\Core\Models\Organization\Organization;
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

$factory->define(Organization::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'hash' => Str::random(10),
        'subdomain' => $faker->domainWord,
        'industry_id' => 1
    ];
});
