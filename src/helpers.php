<?php

use Mostbyte\Multidomain\Managers\DomainManager;

if (!function_exists("mostbyteDomainManager"))
{
    function mostbyteDomainManager ()
    {
        return app(DomainManager::class);
    }
}