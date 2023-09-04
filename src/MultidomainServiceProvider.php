<?php

namespace Mostbyte\Multidomain;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
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

        $this->updateConfigs();
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MostbyteMigrate::class,
                MostbyteRollback::class,
                MostbyteFresh::class,
                MostbyteSchema::class,
                MostbyteInstall::class
            ]);
        }
    }

    private function updateConfigs()
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        $domain = Str::of(parse_url($this->app->make('request')->url(), PHP_URL_PATH))
            ->trim("/")
            ->explode("/")
            ->first();

        mostbyteDomainManager()->setSubdomain($domain);
        mostbyteManager()->updateConfigs($domain);
    }
}
