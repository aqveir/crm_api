<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Modules\Preference\Models\Meta\PreferenceMeta;

use Faker\Generator as Faker;

$factory->define(PreferenceMeta::class, function (Faker $faker) {
    return [
        'key' => $faker->unique()->name(),
    ];
});