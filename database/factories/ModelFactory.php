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

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});

/**
 * Factory definition for model App\Asuransi.
 */
$factory->define(App\Asuransi::class, function ($faker) {
    return [
        // Fields here
    ];
});

/**
 * Factory definition for model App\User.
 */
$factory->define(App\User::class, function ($faker) {
    return [
        // Fields here
    ];
});

/**
 * Factory definition for model App\Diagnosa.
 */
$factory->define(App\Diagnosa::class, function ($faker) {
    return [
        'Kode' => $faker->fillable,
    ];
});

/**
 * Factory definition for model App\Task.
 */
$factory->define(App\Task::class, function ($faker) {
    return [
        // Fields here
    ];
});

/**
 * Factory definition for model App\RequestDemo.
 */
$factory->define(App\RequestDemo::class, function ($faker) {
    return [
        // Fields here
    ];
});

/**
 * Factory definition for model App\HubungiKami.
 */
$factory->define(App\HubungiKami::class, function ($faker) {
    return [
        'emai' => $faker->fillable,
    ];
});
