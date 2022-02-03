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
    'middleware' => 'api',
    'prefix' => 'auth',
    'namespace' => 'App\Http\Controllers',
], function () {
    Route::post('/login', [\App\Http\Controllers\AuthController::class, "login"]);
    Route::post('/registration', [\App\Http\Controllers\AuthController::class, "registration"]);
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, "logout"]);
    Route::post('/refresh', [\App\Http\Controllers\AuthController::class, "refresh"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'user',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/me', [\App\Http\Controllers\AuthController::class, "me"]);
    Route::get('/users', [\App\Http\Controllers\UserController::class, "getUsers"]);
    Route::get('/professors', [\App\Http\Controllers\ProfessorsController::class, "getProfessors"]);
    Route::get('/subjects', [\App\Http\Controllers\SubjectsController::class, "getSubjects"]);
    Route::get('/subjects_id', [\App\Http\Controllers\ProfessorsController::class, "getProfessorsById"]);
});
