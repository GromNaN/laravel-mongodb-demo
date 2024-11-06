<?php

return [
    'driver' => 'meilisearch',

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
