<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToDeleteDirectory;
use Mostbyte\Multidomain\Services\CommandsService;

/**
 * Needs the PostgreSQL database configured in TestCase, like MostbyteSchemaTest.
 */
beforeEach(function () {
    try {
        DB::connection()->getPdo();
    } catch (Throwable $e) {
        $this->markTestSkipped('PostgreSQL is not available: '.$e->getMessage());
    }

    $this->schema = 'test_rollback_'.uniqid();
    DB::statement('DROP SCHEMA IF EXISTS "'.$this->schema.'" CASCADE');
    $this->artisan('mostbyte:schema', ['schema' => $this->schema])->assertSuccessful();
});

afterEach(function () {
    if (isset($this->schema)) {
        DB::statement('DROP SCHEMA IF EXISTS "'.$this->schema.'" CASCADE');
    }
});

it('drops the schema', function () {
    $this->artisan('mostbyte:rollback', ['schema' => $this->schema, '--force' => true])
        ->assertSuccessful();

    expect(app(CommandsService::class)->exists($this->schema))->toBeFalse();
});

it('still succeeds when the media folder cannot be deleted', function () {
    // The S3 driver throws instead of returning false when the prefix is absent.
    // The schema is already dropped by then, so the rollback must not report a
    // failure the caller would retry.
    Storage::shouldReceive('deleteDirectory')
        ->once()
        ->andThrow(UnableToDeleteDirectory::atLocation('public/'.$this->schema));

    $this->artisan('mostbyte:rollback', ['schema' => $this->schema, '--force' => true])
        ->assertSuccessful();

    expect(app(CommandsService::class)->exists($this->schema))->toBeFalse();
});

it('fails on a schema that does not exist', function () {
    $this->artisan('mostbyte:rollback', ['schema' => 'test_rollback_absent_x', '--force' => true])
        ->assertFailed();
});
