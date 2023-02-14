<?php

use Mostbyte\Multidomain\Managers\DomainManager;

if (!function_exists("mostbyteDomainManager"))
{
    function mostbyteDomainManager (): DomainManager
    {
        return app(DomainManager::class);
    }
}