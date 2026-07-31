<?php

use Illuminate\Support\Facades\DB;
use Mostbyte\Multidomain\Services\CommandsService;

/**
 * These tests need the PostgreSQL database configured in TestCase. It is
 * provided by CI; locally the tests are skipped when it is unreachable.
 */
beforeEach(function () {
    try {
        DB::connection()->getPdo();
    } catch (Throwable $e) {
        $this->markTestSkipped('PostgreSQL is not available: '.$e->getMessage());
    }

    // Lowercase only: validateSchema() lowercases the argument.
    $this->schema = 'test_idempotent_'.uniqid();
    DB::statement('DROP SCHEMA IF EXISTS "'.$this->schema.'" CASCADE');
});

afterEach(function () {
    if (isset($this->schema)) {
        DB::statement('DROP SCHEMA IF EXISTS "'.$this->schema.'" CASCADE');
    }
});

it('creates the schema', function () {
    $this->artisan('mostbyte:schema', ['schema' => $this->schema])
        ->assertSuccessful();

    expect(app(CommandsService::class)->exists($this->schema))->toBeTrue();
});

it('succeeds when the schema already exists', function () {
    $this->artisan('mostbyte:schema', ['schema' => $this->schema])
        ->assertSuccessful();

    $this->artisan('mostbyte:schema', ['schema' => $this->schema])
        ->assertSuccessful();

    expect(app(CommandsService::class)->exists($this->schema))->toBeTrue();
});

it('fails on an invalid schema name', function () {
    $this->artisan('mostbyte:schema', ['schema' => 'not a schema!'])
        ->assertFailed();
});
