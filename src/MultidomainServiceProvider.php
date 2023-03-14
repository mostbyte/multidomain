<?php

namespace Mostbyte\Multidomain;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\ServiceProvider;
use Mostbyte\Multidomain\Console\MostbyteFresh;
use Mostbyte\Multidomain\Console\MostbyteMigrate;
use Mostbyte\Multidomain\Console\MostbyteRollback;
use Mostbyte\Multidomain\Console\MostbyteSchema;
use Mostbyte\Multidomain\Fakers\MostbyteImageFaker;
use Mostbyte\Multidomain\Managers\ConsoleManager;
use Mostbyte\Multidomain\Managers\Manager;

class MultidomainServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../config/multidomain.php", "multidomain");
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . "/../config/multidomain.php" => config_path("multidomain.php")
        ], "config");

        /** @var Manager $manager */
        $manager = app(Manager::class);
        $manager->updateConfigs();
        $this->registerCommands();
        $this->singletons();
    }

    protected function singletons()
    {
        $this->app->singleton(ConsoleManager::class);

        $this->app->singleton(Generator::class, function () {
            $faker = Factory::create();
            $faker->addProvider(new MostbyteImageFaker($faker));
            return $faker;
        });
    }

    protected function registerCommands()
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