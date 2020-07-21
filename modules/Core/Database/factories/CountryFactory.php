<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Modules\Core\Models\Common\Country;
use Faker\Generator as Faker;

$factory->define(Country::class, function (Faker $faker) {
    return [
        'alpha2_code' => $faker->unique()->randomNumber($nbDigits = NULL, $strict = false),
        'display_value' => null
    ];
});
