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

// Route::middleware(['api'])->namespace('Api')->prefix('auth')->group(function () {
//     Route::post('register', 'AuthController@register');
//     Route::post('login', 'AuthController@login');
//     Route::post('logout', 'AuthController@logout');
// });

/**
 * ===============
 * /auth
 * -----
 */
// Route::middleware(['auth:api'])->namespace('Api')->prefix('auth')->group(function () {
//     Route::get('refresh', 'AuthController@refresh');
//     Route::get('me', 'AuthController@me');
//     Route::get('validate-token', 'AuthController@validateToken');
//
//     Route::post('update-profile', 'AuthController@updateProfile');
// });


/**
 * ===============
 * RESOURCES APIs
 * -----
 */
Route::middleware(['auth:api'])->namespace('Api\Resources')->group(function () {
    Route::apiResources([
        'users' => 'UserResourceController',
        'roles'	=> 'RoleResourceController',
        'permissions' => 'PermissionResourceController',
    ]);
});

/**
 * ===============
 * RESOURCES APIs (PUBLIC)
 * -----
 */
Route::middleware(['api'])->namespace('Api\Resources')->group(function () {
});
