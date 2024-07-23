<?php

namespace App\Providers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use MongoDB\Laravel\Connection;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MongoDbSessionHandler;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Session::extend('mongodb', function ($app) {
            /** @var Connection $connection */
            $connection = $app->get('db')->connection('mongodb');

            return new MongoDbSessionHandler(
                $connection->getMongoClient(),
                [
                    'database' => $connection->getDatabaseName(),
                    'collection' => 'sessions',
                ],
            );
        });
    }
}
