<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Modules\Core\Models\Lookup\Lookup;
use Faker\Generator as Faker;

$factory->define(Lookup::class, function (Faker $faker) {
    return [
        'key' => '',
        'display_value' => null,
    ];
});
