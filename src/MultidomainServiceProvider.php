<?php

namespace Mostbyte\Multidomain;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Mostbyte\Multidomain\Console\MostbyteFresh;
use Mostbyte\Multidomain\Console\MostbyteMigrate;
use Mostbyte\Multidomain\Console\MostbyteRollback;
use Mostbyte\Multidomain\Console\MostbyteSchema;
use Mostbyte\Multidomain\Fakers\MostbyteImageFaker;
use Mostbyte\Multidomain\Managers\ConsoleManager;
use Mostbyte\Multidomain\Managers\DomainManager;
use Mostbyte\Multidomain\Middlewares\MultidomainMiddleware;

class MultidomainServiceProvider extends RouteServiceProvider
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

    public function boot(): void
    {
        $this->middleware(MultidomainMiddleware::class)->prefix('{domain}');
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MostbyteMigrate::class,
                MostbyteRollback::class,
                MostbyteFresh::class,
                MostbyteSchema::class,
            ]);
        }
    }
}