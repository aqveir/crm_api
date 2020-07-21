<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Modules\Core\Models\Common\Currency;
use Faker\Generator as Faker;

$factory->define(Currency::class, function (Faker $faker) {
    return [
        'display_value' => null,
    ];
});
