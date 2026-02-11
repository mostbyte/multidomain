<?php

use Mostbyte\Multidomain\Managers\DomainManager;

it('sets and gets subdomain', function () {
    $manager = app(DomainManager::class);

    $manager->setSubdomain('test-tenant');
    expect($manager->getSubDomain())->toBe('test-tenant');
});

it('returns fluent interface from setSubdomain', function () {
    $manager = app(DomainManager::class);
    $result = $manager->setSubdomain('tenant');

    expect($result)->toBeInstanceOf(DomainManager::class);
});

it('gets full domain from request', function () {
    $manager = app(DomainManager::class);
    $fullDomain = $manager->getFullDomain();

    expect($fullDomain)->toBeString();
});

it('gets locale from request', function () {
    $manager = app(DomainManager::class);
    $locale = $manager->getLocale();

    expect($locale)->toBeString();
});
