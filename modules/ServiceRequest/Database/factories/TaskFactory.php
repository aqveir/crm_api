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
    
    $dtStartAtDate = $faker->dateTimeBetween($startDate = '-30 days', $endDate = '30 days', $timezone = null);
    $dtEndAtDate = $faker->dateTimeBetween($startDate = $dtStartAtDate, $endDate = '30 days', $timezone = null);
    $statusId = $faker->numberBetween(78, 82);

    if (Carbon::parse($dtEndAtDate) < Carbon::now()) {
        $isCompleted = $faker->boolean;
    } //End if

    $dtCompletedDate = null;
    if ($isCompleted) {
        $statusId = 80;
        $dtCompletedDate = $faker->dateTimeBetween($startDate = $dtEndAtDate, $endDate = 'now', $timezone = null);
    } //End if
    
    return [
        'subject' => $faker->word,
        'description' => $faker->text($maxNbChars = 100),
        'start_at' => $dtStartAtDate,
        'end_at' => $dtEndAtDate,
        'completed_at' => $dtCompletedDate,
        'status_id' => $statusId,
        'subtype_id' => $faker->numberBetween(71, 74),
        'priority_id' => $faker->numberBetween(75, 77),
    ];
});
