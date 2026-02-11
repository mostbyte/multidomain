<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Mostbyte\Multidomain\Services\CommandsService;

beforeEach(function () {
    $this->service = new CommandsService;
});

it('validates a valid schema name', function () {
    $result = $this->service->validateSchema('my-tenant');
    expect($result)->toBe('my-tenant');
});

it('lowercases the schema name', function () {
    $result = $this->service->validateSchema('MyTenant');
    expect($result)->toBe('mytenant');
});

it('allows numbers in schema names', function () {
    $result = $this->service->validateSchema('tenant123');
    expect($result)->toBe('tenant123');
});

it('allows underscores in schema names', function () {
    $result = $this->service->validateSchema('my_tenant');
    expect($result)->toBe('my_tenant');
});

it('allows hyphens in schema names', function () {
    $result = $this->service->validateSchema('my-tenant');
    expect($result)->toBe('my-tenant');
});

it('rejects schema names with special characters', function () {
    $this->service->validateSchema('my schema!');
})->throws(ValidationException::class);

it('rejects schema names with dots', function () {
    $this->service->validateSchema('my.tenant');
})->throws(ValidationException::class);

it('rejects schema names exceeding max length', function () {
    $this->service->validateSchema(str_repeat('a', 51));
})->throws(ValidationException::class);

it('accepts schema names at max length', function () {
    $result = $this->service->validateSchema(str_repeat('a', 50));
    expect($result)->toBe(str_repeat('a', 50));
});

it('uses config for max length', function () {
    config(['multidomain.schema_validation.max_length' => 10]);
    $this->service->validateSchema(str_repeat('a', 11));
})->throws(ValidationException::class);

it('uses config for regex pattern', function () {
    config(['multidomain.schema_validation.regex' => '/^[a-z]+$/']);
    $this->service->validateSchema('tenant-1');
})->throws(ValidationException::class);

it('invalidates schema cache', function () {
    Cache::put('multidomain_schema_exists:test-tenant', true, 3600);
    expect(Cache::has('multidomain_schema_exists:test-tenant'))->toBeTrue();

    CommandsService::invalidateSchemaCache('test-tenant');
    expect(Cache::has('multidomain_schema_exists:test-tenant'))->toBeFalse();
});

it('uses configured cache prefix for invalidation', function () {
    config(['multidomain.cache.prefix' => 'custom_prefix']);
    Cache::put('custom_prefix:my-tenant', true, 3600);

    CommandsService::invalidateSchemaCache('my-tenant');
    expect(Cache::has('custom_prefix:my-tenant'))->toBeFalse();
});
