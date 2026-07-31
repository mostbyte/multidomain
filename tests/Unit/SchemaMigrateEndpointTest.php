<?php

use Illuminate\Support\Facades\DB;
use Mostbyte\Multidomain\Services\CommandsService;

/**
 * End-to-end contract of the provisioning endpoint: Identity calls it with the
 * shared key and decides on the response body, so both the happy path and the
 * failure path have to keep returning success/status/message.
 *
 * These tests need the PostgreSQL database configured in TestCase. It is
 * provided by CI; locally the tests are skipped when it is unreachable.
 */
beforeEach(function () {
    try {
        DB::connection()->getPdo();
    } catch (Throwable $e) {
        $this->markTestSkipped('PostgreSQL is not available: '.$e->getMessage());
    }

    config(['multidomain.api_key' => 'secret']);

    // Lowercase only: validateSchema() lowercases the argument.
    $this->schema = 'test_endpoint_'.uniqid();
    DB::statement('DROP SCHEMA IF EXISTS "'.$this->schema.'" CASCADE');
});

afterEach(function () {
    if (isset($this->schema)) {
        DB::statement('DROP SCHEMA IF EXISTS "'.$this->schema.'" CASCADE');
    }
});

it('creates the schema through the endpoint with a valid api key', function () {
    $this->postJson("/{$this->schema}/multidomain/schema", [], ['X-API-KEY' => 'secret'])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('status', 0);

    expect(app(CommandsService::class)->exists($this->schema))->toBeTrue();
});

it('drops the schema through the endpoint', function () {
    $this->postJson("/{$this->schema}/multidomain/schema", [], ['X-API-KEY' => 'secret'])
        ->assertOk();

    $this->postJson("/{$this->schema}/multidomain/rollback", [], ['X-API-KEY' => 'secret'])
        ->assertOk()
        ->assertJsonPath('success', true);

    expect(app(CommandsService::class)->exists($this->schema))->toBeFalse();
});

it('reports a failed rollback of an unknown schema instead of a 5xx', function () {
    $response = $this->postJson("/{$this->schema}/multidomain/rollback", [], ['X-API-KEY' => 'secret'])
        ->assertOk()
        ->assertJsonPath('success', false);

    expect($response->json('status'))->not->toBe(0)
        ->and($response->json('message'))->toContain("Schema \"{$this->schema}\" not found!");
});
