<?php

namespace Mostbyte\Multidomain;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Mostbyte\Multidomain\Commands\MultidomainCommand;

class MultidomainServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('multidomain')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_multidomain_table')
            ->hasCommand(MultidomainCommand::class);
    }
}
