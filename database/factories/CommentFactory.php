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

$factory->define(\App\Comment::class, function (Faker $faker) {

    return [
        'user_id' => App\User::take(20)->pluck('id')->random(),
        'article_id' => App\Article::take(50)->pluck('id')->random(),
        'content' => $faker->realText(255),
    ];
});
