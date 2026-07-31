<?php

use Illuminate\Support\Facades\Route;
use Mostbyte\Multidomain\Http\Middlewares\MultidomainMiddleware;
use Mostbyte\Multidomain\Http\Middlewares\VerifyApiKeyMiddleware;

/**
 * The package routes are registered on boot, so a config override made inside
 * a test only takes effect once the route file is loaded again.
 */
function reloadMultidomainRoutes(): void
{
    require __DIR__.'/../../routes/api.php';

    Route::getRoutes()->refreshNameLookups();
    Route::getRoutes()->refreshActionLookups();
}

function multidomainRouteMiddleware(): array
{
    return Route::getRoutes()->getByName('mostbyte.multidomain.type')->gatherMiddleware();
}

it('registers the api key middleware by default', function () {
    reloadMultidomainRoutes();

    expect(multidomainRouteMiddleware())->toContain(VerifyApiKeyMiddleware::class);
});

it('keeps the api key middleware when the application overrides the middleware config', function () {
    // A consumer that published config/multidomain.php before v2.3.0 has a
    // middleware list without the api key check.
    config([
        'multidomain.api_key' => 'secret',
        'multidomain.middleware' => [MultidomainMiddleware::class, 'api'],
    ]);

    reloadMultidomainRoutes();

    expect(multidomainRouteMiddleware())->toContain(VerifyApiKeyMiddleware::class);
});

it('rejects an unauthenticated request when the middleware config omits the api key check', function () {
    config([
        'multidomain.api_key' => 'secret',
        'multidomain.middleware' => [MultidomainMiddleware::class, 'api'],
    ]);

    reloadMultidomainRoutes();

    $this->postJson('/tenant-1/multidomain/rollback')->assertStatus(401);
});

it('rejects a request with a wrong key when the middleware config omits the api key check', function () {
    config([
        'multidomain.api_key' => 'secret',
        'multidomain.middleware' => [MultidomainMiddleware::class, 'api'],
    ]);

    reloadMultidomainRoutes();

    $this->postJson('/tenant-1/multidomain/rollback', [], ['X-API-KEY' => 'wrong'])
        ->assertStatus(403);
});

it('fails closed when the middleware config omits the api key check and no key is configured', function () {
    config([
        'multidomain.api_key' => null,
        'multidomain.middleware' => [MultidomainMiddleware::class, 'api'],
    ]);

    reloadMultidomainRoutes();

    $this->postJson('/tenant-1/multidomain/rollback', [], ['X-API-KEY' => 'anything'])
        ->assertStatus(403);
});

it('does not register the api key middleware twice', function () {
    config([
        'multidomain.middleware' => [
            VerifyApiKeyMiddleware::class,
            MultidomainMiddleware::class,
            'api',
        ],
    ]);

    reloadMultidomainRoutes();

    $occurrences = array_count_values(array_filter(
        multidomainRouteMiddleware(),
        fn ($middleware) => $middleware === VerifyApiKeyMiddleware::class
    ));

    expect($occurrences[VerifyApiKeyMiddleware::class])->toBe(1);
});
