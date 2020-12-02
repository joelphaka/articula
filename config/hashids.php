<?php

/**
 * Copyright (c) Vincent Klaiber.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/vinkla/laravel-hashids
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'main',
    \App\User::class => \App\User::class,

    /*
    |--------------------------------------------------------------------------
    | Hashids Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */

    'connections' => [

        'main' => [
            'salt' => '5498519d5d683165017cf3f4c1220e3e4fb8603baf39c9aa33551e7418ad4d63059ca30bdcc6fd4a81b9c56b2fa5f7fcfe12f7c8ef30f32f7d29929c4eff81d5',
            'length' => 8,
        ],

        \App\User::class => [
            'salt' => \App\User::class . 'e659ecade4cc9106f7213241e4f9d5f0dd1c3ec1a282039f6a1abbcc3db050e12819d0d27223b140765e4e0f5633a0085ada8f9d85a5070fc3a77f8a0908da34',
            'length' => 32,
        ],
        \App\Article::class => [
            'salt' => \App\Article::class . 'bf01d1091e09bcde90b1da7b88d0af9bcebc709d81507bf2e807637c768491eb7c45b9ef25d301beb1a8ba2252e0752302221e84cedf2a594c878a6e4afd7363',
            'length' => 8,
        ],

    ],

];
