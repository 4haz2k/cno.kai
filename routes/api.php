<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('/user/login', [\App\Http\Controllers\AuthController::class, "login"]);
    Route::post('/user/registration', [\App\Http\Controllers\AuthController::class, "registration"]);
    Route::post('/user/logout', [\App\Http\Controllers\AuthController::class, "logout"]);
    Route::post('/refresh', [\App\Http\Controllers\AuthController::class, "refresh"]);
    Route::post('/user/me', [\App\Http\Controllers\AuthController::class, "me"]);
});
