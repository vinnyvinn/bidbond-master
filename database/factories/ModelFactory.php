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


$factory->define(App\Models\BidBondTemplate::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'content' => $faker->randomHtml(3, 5)
    ];
});

$factory->define(App\Models\CounterParty::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'physical_address' => $faker->address,
        'postal_address' => $faker->address,
        'postal_code_id' => App\Models\PostalCode::all()->random()->id,
        'category_secret' => App\Models\Category::all()->random()->secret
    ];
});