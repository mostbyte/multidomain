<?php

use Mostbyte\Multidomain\Managers\ConsoleManager;
use Mostbyte\Multidomain\Managers\DomainManager;

it('registers DomainManager as singleton', function () {
    $instance1 = app(DomainManager::class);
    $instance2 = app(DomainManager::class);

    expect($instance1)->toBe($instance2);
});

it('registers ConsoleManager as singleton', function () {
    $instance1 = app(ConsoleManager::class);
    $instance2 = app(ConsoleManager::class);

    expect($instance1)->toBe($instance2);
});

it('loads config', function () {
    expect(config('multidomain'))->toBeArray();
    expect(config('multidomain'))->toHaveKeys([
        'driver',
        'default_schema',
        'excluded_schemas',
        'excluded_schema_prefixes',
        'schema_validation',
        'cache',
        'filesystem_disk',
    ]);
});

it('throws exception for non-pgsql driver', function () {
    config(['database.default' => 'mysql']);
    config(['database.connections.mysql.driver' => 'mysql']);
    config(['multidomain.driver' => null]);

    \Mostbyte\Multidomain\MultidomainServiceProvider::ensurePostgresDriver();
})->throws(RuntimeException::class, 'Multidomain requires a PostgreSQL database connection');

it('does not throw for pgsql driver', function () {
    config(['database.default' => 'pgsql']);
    config(['database.connections.pgsql.driver' => 'pgsql']);

    \Mostbyte\Multidomain\MultidomainServiceProvider::ensurePostgresDriver();

    expect(true)->toBeTrue();
});

it('uses multidomain.driver config when set', function () {
    config(['multidomain.driver' => 'custom_pgsql']);
    config(['database.connections.custom_pgsql.driver' => 'pgsql']);

    \Mostbyte\Multidomain\MultidomainServiceProvider::ensurePostgresDriver();

    expect(true)->toBeTrue();
});
