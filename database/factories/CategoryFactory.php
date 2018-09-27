<?php

use Faker\Generator as Faker;

$factory->define(App\Category::class, function (Faker $faker) {
    return [
        'name' => $faker->name(),
        'division_id' => $faker->numberBetween(1, 12),
    ];
});