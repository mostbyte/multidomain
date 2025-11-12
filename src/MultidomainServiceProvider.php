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
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MultidomainServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('multidomain')
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
}
