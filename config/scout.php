<?php

return [
    'driver' => env('SCOUT_DRIVER', 'mongodb'),

    'mongodb' => [
        'connection' => env('SCOUT_MONGODB_CONNECTION', 'mongodb'),
    ],
    'prefix' => env('SCOUT_PREFIX', 'scout_'),

    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
        'key' => env('MEILISEARCH_KEY'),
        'index-settings' => [
            'movies' => [
                'filterableAttributes' => ['title', 'plot', 'year'],
                'displayedAttributes' => ['*'],
                /*'displayedAttributes' => [
                    'id',
                    'title',
                    'poster', // Used in the preview
                    'comments'
                ],*/
            ],
        ],
    ],
];
