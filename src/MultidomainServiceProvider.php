<?php

namespace Mostbyte\Multidomain;

use Illuminate\Support\ServiceProvider;
use Mostbyte\Multidomain\Console\MostbyteMigrate;
use Mostbyte\Multidomain\Managers\Manager;

class MultidomainServiceProvider extends ServiceProvider
{
    public function boot()
    {
        /** @var Manager $manager */
        $manager = app(Manager::class);
        $manager->updateConfigs();
        $this->registerCommands();
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MostbyteMigrate::class,
            ]);
        }
    }
}