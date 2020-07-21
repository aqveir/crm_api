<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Modules\Core\Models\Role\Role;
use Faker\Generator as Faker;

$factory->define(Role::class, function (Faker $faker) {
    return [
        'org_id' => 0,
        'key' => '',
        'display_value' => null,
    ];
});
