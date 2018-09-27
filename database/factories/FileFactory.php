<?php

use Faker\Generator as Faker;

$factory->define(App\ArchiveFile::class, function (Faker $faker) {
    return [
        'date' => $faker->date(),
        'content' => $faker->realText(),
        'division_id' => $faker->numberBetween(1, 12),
        'file_name' => $faker->name()
    ];
});
