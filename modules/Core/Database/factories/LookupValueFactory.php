<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Modules\Core\Models\Lookup\LookupValue;
use Faker\Generator as Faker;

$factory->define(LookupValue::class, function (Faker $faker) {
    return [
        'key' => '',
        'display_value' => null,
    ];
});
