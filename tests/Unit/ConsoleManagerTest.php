<?php

use Mostbyte\Multidomain\Managers\ConsoleManager;

it('has empty schema by default', function () {
    $manager = new ConsoleManager;
    expect($manager->getSchema())->toBe('');
});

it('sets and gets schema', function () {
    $manager = new ConsoleManager;
    $manager->setSchema('test-tenant');

    expect($manager->getSchema())->toBe('test-tenant');
});
