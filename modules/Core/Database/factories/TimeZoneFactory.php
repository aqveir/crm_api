<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Modules\Core\Models\Lookup\TimeZone;
use Faker\Generator as Faker;

$factory->define(TimeZone::class, function (Faker $faker) {
    return [
        'country_id' => 1,
        'iso3_code' => 'abcd',
        'display_value' => null,
    ];
});
