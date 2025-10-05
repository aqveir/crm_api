<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Modules\Core\Models\Common\Configuration;
use Faker\Generator as Faker;

$factory->define(Configuration::class, function (Faker $faker) {
    return [
        'type_id' => 1,
        'key' => $faker->unique()->randomNumber($nbDigits = NULL, $strict = false),
        'display_value' => null
    ];
});
