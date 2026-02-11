<?php

use Mostbyte\Multidomain\Managers\DomainManager;
use Mostbyte\Multidomain\Managers\Manager;

if (! function_exists('mostbyteDomainManager')) {
    function mostbyteDomainManager(): DomainManager
    {
        return app(DomainManager::class);
    }
}

if (! function_exists('mostbyteManager')) {
    function mostbyteManager(): Manager
    {
        return app(Manager::class);
    }
}
