<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Voucher::class, function (Faker\Generator $faker) {

    return [
        'start_date' => $faker->dateTimeBetween('-10 days', '-5 days'),
        'end_date' => $faker->dateTimeBetween('+2 days', '+20 days'),
        'discount' => [10, 15, 20, 25][rand(0, 3)]
    ];
});
