<?php

use Faker\Generator as Faker;

$factory->define(App\Division::class, function (Faker $faker) {
    return [
        'div_name' => $faker->name(),
    ];
});
