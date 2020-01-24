<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Patient;
use Faker\Generator as Faker;

$factory->define(Patient::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'familyName' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'date_of_birth' => $faker->date('Y-m-d', '2000-01-01'),
        'phone'  => $faker->phoneNumber
    ];
});
