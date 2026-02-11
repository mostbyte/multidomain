<?php

namespace Mostbyte\Multidomain;

use Faker\Factory;
use Faker\Generator;
use Mostbyte\Multidomain\Commands\MostbyteFresh;
use Mostbyte\Multidomain\Commands\MostbyteInstall;
use Mostbyte\Multidomain\Commands\MostbyteMigrate;
use Mostbyte\Multidomain\Commands\MostbyteRollback;
use Mostbyte\Multidomain\Commands\MostbyteSchema;
use Mostbyte\Multidomain\Fakers\MostbyteImageFaker;
use Mostbyte\Multidomain\Managers\ConsoleManager;
use Mostbyte\Multidomain\Managers\DomainManager;
use RuntimeException;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MultidomainServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('multidomain')
            ->hasConfigFile()
            ->hasRoute('api')
            ->hasCommands([
                MostbyteMigrate::class,
                MostbyteRollback::class,
                MostbyteSchema::class,
                MostbyteFresh::class,
                MostbyteInstall::class,
            ]);

        $this->app->singleton(DomainManager::class);
        $this->app->singleton(ConsoleManager::class);

        $this->app->singleton(Generator::class, function () {
            $faker = Factory::create();
            $faker->addProvider(new MostbyteImageFaker($faker));

            return $faker;
        });
    }

    public function packageBooted(): void
    {
        $this->app->resolving(DomainManager::class, function () {
            static $checked = false;
            if ($checked) {
                return;
            }
            $checked = true;
            static::ensurePostgresDriver();
        });
    }

    public static function ensurePostgresDriver(): void
    {
        $driver = config('multidomain.driver') ?? config('database.default');
        $driverName = config("database.connections.$driver.driver");

        if ($driverName && $driverName !== 'pgsql') {
            throw new RuntimeException(
                'Multidomain requires a PostgreSQL database connection. '
                ."The [{$driver}] connection uses [{$driverName}]. "
                .'Please configure a PostgreSQL connection.'
            );
        }
    }
}
