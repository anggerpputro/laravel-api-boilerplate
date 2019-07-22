<?php

use Illuminate\Http\Request;

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

Route::middleware(['api'])->namespace('Api')->group(function () {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
});

/**
 * ===============
 * /auth
 * -----
 */
Route::middleware(['auth:api'])->namespace('Api')->prefix('auth')->group(function () {
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

/**
 * ===============
 * RESOURCES APIs
 * -----
 */
Route::middleware(['auth:api'])->namespace('Api\Resources')->group(function () {
    Route::apiResources([
        'users'		=> 'UserResourceController',
    ]);
});
