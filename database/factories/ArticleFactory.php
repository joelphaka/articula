<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

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

$factory->define(\App\Article::class, function (Faker $faker) {

    $userId = App\User::all()->pluck('id')->random();
    $title = $faker->text(60);
    $content= $faker->realText(360);

    return [
        'user_id' => $userId,
        'title' => $title,
        'content' => $content,
    ];
});
