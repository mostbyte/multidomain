<?php

namespace Mostbyte\Multidomain;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Mostbyte\Auth\Middleware\IdentityAuth;
use Mostbyte\Multidomain\Console\MostbyteFresh;
use Mostbyte\Multidomain\Console\MostbyteInstall;
use Mostbyte\Multidomain\Console\MostbyteMigrate;
use Mostbyte\Multidomain\Console\MostbyteRollback;
use Mostbyte\Multidomain\Console\MostbyteSchema;
use Mostbyte\Multidomain\Fakers\MostbyteImageFaker;
use Mostbyte\Multidomain\Managers\ConsoleManager;
use Mostbyte\Multidomain\Managers\DomainManager;
use Mostbyte\Multidomain\Middlewares\MultidomainMiddleware;

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

        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        Route::group([
            'prefix' => '{domain}/multidomain',
            'as' => 'mostbyte.multidomain.',
            'middleware' => [
                MultidomainMiddleware::class,
                IdentityAuth::class,
                'api'
            ],
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/routes/api.php');
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
