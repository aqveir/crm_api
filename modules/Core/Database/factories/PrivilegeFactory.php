<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Modules\Core\Models\Privilege\Privilege;
use Faker\Generator as Faker;

$factory->define(Privilege::class, function (Faker $faker) {
    return [
        'key' => '',
        'display_value' => null,
    ];
});
