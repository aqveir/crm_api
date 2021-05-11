<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Modules\ServiceRequest\Models\Event as ServiceRequestEvent;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Carbon\Carbon;

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

$factory->define(ServiceRequestEvent::class, function (Faker $faker) {

    $dtStartDate = $faker->dateTimeBetween($startDate = '-30 days', $endDate = '30 days', $timezone = null);

    if (Carbon::parse($dtStartDate) < Carbon::now()) {
        $dtEndDate = $faker->dateTimeBetween($startDate = $dtStartDate, $endDate = 'now', $timezone = null);
    } else {
        $dtEndDate = $faker->dateTimeInInterval($startDate = $startDate, $interval = '+ 3 days', $timezone = null);
    } //End if
    
    return [
        'subject' => $faker->word,
        'description' => $faker->text($maxNbChars = 100),
        'start_at' => $dtStartDate,
        'end_at' => $dtEndDate
    ];
});
