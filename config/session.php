<?php


return [
    'driver' => 'mongodb',
    'store' => 'mongodb',
    'ttl' => 120,
    'options' => [
        'database' => 'laravel',
        'collection' => 'sessions',
    ],
];
