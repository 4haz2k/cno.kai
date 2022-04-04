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
//    Route::post('/logout', [\App\Http\Controllers\AuthController::class, "logout"]);
//    Route::post('/refresh', [\App\Http\Controllers\AuthController::class, "refresh"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'user',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/me', [\App\Http\Controllers\AuthController::class, "me"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'orders',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/all', [\App\Http\Controllers\OrdersController::class, "getOrders"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'professors',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/all_with_subjects', [\App\Http\Controllers\ProfessorsController::class, "getProfessors"]);
    Route::get('/get_by_subject_id', [\App\Http\Controllers\ProfessorsController::class, "getProfessorsById"]);
    Route::post('/short/all', [\App\Http\Controllers\ProfessorsController::class, "getShortProfessors"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'students',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/all', [\App\Http\Controllers\StudentsController::class, "getStudents"]);
    Route::post('/get_by_group_and_speciality', [\App\Http\Controllers\StudentsController::class, "getByGroupAndSpeciality"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'subjects',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::get('/all', [\App\Http\Controllers\SubjectsController::class, "getSubjects"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'groups',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/all', [\App\Http\Controllers\GroupsController::class, "getGroups"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'specialities',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/all', [\App\Http\Controllers\SpecialitiesController::class, "getSpecialities"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'other',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/statistic/orders', [\App\Http\Controllers\OthersController::class, "getStatistic"]);
});
