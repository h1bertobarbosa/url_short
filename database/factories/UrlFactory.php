<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Url;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Url::class, function (Faker $faker) {
    return [
        'user_name' => $faker->userName,
        'original_url' => $faker->url,
        'url_code' => Str::random(16),
        'clicks' => 0
    ];
});
