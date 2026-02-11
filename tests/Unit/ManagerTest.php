<?php

use Mostbyte\Multidomain\Managers\DomainManager;
use Mostbyte\Multidomain\Managers\Manager;

it('returns default schema when no subdomain set', function () {
    $domainManager = app(DomainManager::class);
    $manager = new Manager($domainManager);

    expect($manager->getSchema())->toBe('public');
});

it('uses configured default schema', function () {
    config(['multidomain.default_schema' => 'main']);
    $domainManager = app(DomainManager::class);
    $manager = new Manager($domainManager);

    expect($manager->getSchema())->toBe('main');
});

it('returns subdomain as schema when set', function () {
    $domainManager = app(DomainManager::class);
    $domainManager->setSubdomain('my-tenant');
    $manager = new Manager($domainManager);

    expect($manager->getSchema())->toBe('my-tenant');
});

it('updates app config', function () {
    $domainManager = app(DomainManager::class);
    $domainManager->setSubdomain('my-tenant');
    $manager = new Manager($domainManager);

    $manager->updateAppConfig('my-tenant');

    expect(config('app.name'))->toBe('MY-TENANT');
});

it('updates database config with configured driver', function () {
    config(['multidomain.driver' => 'pgsql']);
    $domainManager = app(DomainManager::class);
    $domainManager->setSubdomain('test');
    $manager = new Manager($domainManager);

    $manager->updateDatabaseConfig('test-schema');

    expect(config('database.connections.pgsql.schema'))->toBe('test-schema');
});

it('updates log config with schema-specific paths', function () {
    $domainManager = app(DomainManager::class);
    $domainManager->setSubdomain('my-tenant');
    $manager = new Manager($domainManager);

    $manager->updateLogConfig('my-tenant');

    $date = now()->toDateString();
    expect(config('logging.channels.single.path'))
        ->toContain('my-tenant')
        ->toContain($date);
});

it('updates telescope config', function () {
    $domainManager = app(DomainManager::class);
    $manager = new Manager($domainManager);

    $manager->updateTelescopeConfig('my-tenant');

    expect(config('telescope.path'))->toBe('my-tenant/telescope');
});

it('updates filesystem config with schema subdirectory', function () {
    config([
        'filesystems.disks.public.root' => '/storage/app/public',
        'filesystems.disks.public.url' => '/storage',
    ]);
    $domainManager = app(DomainManager::class);
    $domainManager->setSubdomain('my-tenant');
    $manager = new Manager($domainManager);

    $manager->updateFilesystemConfig('public');

    expect(config('filesystems.disks.public.root'))->toContain('my-tenant');
    expect(config('filesystems.disks.public.url'))->toContain('my-tenant');
});
