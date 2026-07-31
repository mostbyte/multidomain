<?php

use Illuminate\Support\Facades\Route;
use Mostbyte\Multidomain\Http\Controllers\SchemaMigrateController;
use Mostbyte\Multidomain\Http\Middlewares\MultidomainMiddleware;
use Mostbyte\Multidomain\Http\Middlewares\VerifyApiKeyMiddleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => '{domain}/multidomain',
    'as' => 'mostbyte.multidomain.',
    // These routes create, migrate and drop tenant schemas, so the api key
    // check is always prepended: an application that publishes the config and
    // overrides the middleware list must not be able to drop it silently.
    'middleware' => array_values(array_unique(array_merge(
        [VerifyApiKeyMiddleware::class],
        (array) config('multidomain.middleware', [
            VerifyApiKeyMiddleware::class,
            MultidomainMiddleware::class,
            'api',
        ])
    ))),
], function () {
    Route::post('{type}', SchemaMigrateController::class)->name('type');
});
