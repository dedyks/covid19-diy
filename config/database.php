<?php

return  [
    'default' => 'mongodb',

    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', 'localhost'),
            'database' => env('DB_DATABASE', ''),
            'username' => env('DB_USERNAME', ''),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
        ],

        'mongodb' => [
            'driver' => 'mongodb',
            'host' => [
                env('DB_HOST1'),
                env('DB_HOST2'),
                env('DB_HOST3'),
        ],
            'port' => env('DB_PORT', 27017),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'options' => [
                'ssl' => true,
                'database' => 'admin', // sets the authentication database required by mongo 3
            ],
        ],
    ],
];
