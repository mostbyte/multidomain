<?php

namespace Mostbyte\Multidomain\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Mostbyte\Multidomain\Multidomain setTenant(string $schema)
 * @method static string currentSchema()
 * @method static \Mostbyte\Multidomain\Managers\DomainManager domainManager()
 * @method static \Mostbyte\Multidomain\Managers\Manager manager()
 * @method static array schemas()
 * @method static bool schemaExists(string $schema)
 *
 * @see \Mostbyte\Multidomain\Multidomain
 */
class Multidomain extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Mostbyte\Multidomain\Multidomain::class;
    }
}
