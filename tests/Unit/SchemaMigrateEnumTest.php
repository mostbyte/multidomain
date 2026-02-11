<?php

use Mostbyte\Multidomain\Enums\SchemaMigrateEnum;

it('has three cases', function () {
    expect(SchemaMigrateEnum::cases())->toHaveCount(3);
});

it('has correct values', function () {
    expect(SchemaMigrateEnum::MIGRATE->value)->toBe('migrate');
    expect(SchemaMigrateEnum::SCHEMA->value)->toBe('schema');
    expect(SchemaMigrateEnum::ROLLBACK->value)->toBe('rollback');
});

it('checks type with is method using single enum', function () {
    expect(SchemaMigrateEnum::MIGRATE->is(SchemaMigrateEnum::MIGRATE))->toBeTrue();
    expect(SchemaMigrateEnum::MIGRATE->is(SchemaMigrateEnum::SCHEMA))->toBeFalse();
});

it('checks type with is method using array', function () {
    expect(SchemaMigrateEnum::MIGRATE->is([SchemaMigrateEnum::MIGRATE, SchemaMigrateEnum::SCHEMA]))->toBeTrue();
    expect(SchemaMigrateEnum::ROLLBACK->is([SchemaMigrateEnum::MIGRATE, SchemaMigrateEnum::SCHEMA]))->toBeFalse();
});
