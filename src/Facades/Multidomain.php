<?php

namespace Mostbyte\Multidomain\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mostbyte\Multidomain\Multidomain
 */
class Multidomain extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Mostbyte\Multidomain\Multidomain::class;
    }
}
