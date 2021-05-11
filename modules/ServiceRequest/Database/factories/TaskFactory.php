<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Modules\ServiceRequest\Models\Task;
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

$factory->define(Task::class, function (Faker $faker) {

    $isCompleted = false;
    
    $dtScheduledDate = $faker->dateTimeBetween($startDate = '-30 days', $endDate = '30 days', $timezone = null);

    if (Carbon::parse($dtScheduledDate) < Carbon::now()) {
        $isCompleted = $faker->boolean;
    } //End if

    $dtCompletedDate = null;
    if ($isCompleted) {
        $dtCompletedDate = $faker->dateTimeBetween($startDate = $dtScheduledDate, $endDate = 'now', $timezone = null);
    } //End if
    
    return [
        'subject' => $faker->word,
        'description' => $faker->text($maxNbChars = 100),
        'is_scheduled' => 1,
        'scheduled_at' => $dtScheduledDate,
        'is_completed' => $isCompleted,
        'completed_at' => $dtCompletedDate
    ];
});
