<?php

namespace Mostbyte\Multidomain\Tests;

use Mostbyte\Multidomain\MultidomainServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            MultidomainServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Multidomain' => \Mostbyte\Multidomain\Facades\Multidomain::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'pgsql');
        $app['config']->set('database.connections.pgsql', [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'multidomain_test'),
            'username' => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', ''),
            'schema' => 'public',
        ]);
    }
}
