<?php

namespace Mostbyte\Multidomain;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\ServiceProvider;
use Mostbyte\Multidomain\Console\MostbyteFresh;
use Mostbyte\Multidomain\Console\MostbyteInstall;
use Mostbyte\Multidomain\Console\MostbyteMigrate;
use Mostbyte\Multidomain\Console\MostbyteRollback;
use Mostbyte\Multidomain\Console\MostbyteSchema;
use Mostbyte\Multidomain\Fakers\MostbyteImageFaker;
use Mostbyte\Multidomain\Managers\ConsoleManager;
use Mostbyte\Multidomain\Managers\DomainManager;

class MultidomainServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->registerCommands();

        $this->app->singleton(DomainManager::class);
        $this->app->singleton(ConsoleManager::class);

        $this->app->singleton(Generator::class, function () {
            $faker = Factory::create();
            $faker->addProvider(new MostbyteImageFaker($faker));
            return $faker;
        });
    }

    protected function registerCommands(): void
    {
        $this->commands([
            MostbyteMigrate::class,
            MostbyteRollback::class,
            MostbyteSchema::class,
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                MostbyteFresh::class,
                MostbyteInstall::class
            ]);
        }
    }
}