<?php

use Illuminate\Support\Facades\Route;
use Mostbyte\Multidomain\Http\Controllers\SchemaMigrateController;

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

Route::post("{type}", SchemaMigrateController::class)
    ->name('type');