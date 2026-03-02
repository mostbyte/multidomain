<?php

use Illuminate\Support\Facades\Route;
use Mostbyte\Multidomain\Http\Controllers\SchemaMigrateController;
use Mostbyte\Multidomain\Http\Middlewares\MultidomainMiddleware;

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
    'middleware' => config('multidomain.middleware', [
        MultidomainMiddleware::class,
        'api',
    ]),
], function () {
    Route::post('{type}', SchemaMigrateController::class)->name('type');
});
