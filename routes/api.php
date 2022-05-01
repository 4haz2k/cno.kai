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
    'prefix' => 'download',
    'namespace' => 'App\Http\Controllers',
], function () {
    Route::get('/treaty', [\App\Http\Controllers\FilesController::class, "getTreaty"]);
});

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
    Route::post('/update/professor', [\App\Http\Controllers\UserController::class, "updateProfessorData"]);
    Route::post('/update/student', [\App\Http\Controllers\UserController::class, "updateStudentData"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'orders',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/all', [\App\Http\Controllers\OrdersController::class, "getOrders"]);
    Route::post('/getSingle', [\App\Http\Controllers\OrdersController::class, "getSingleOrder"]);
    Route::post('/professor/all', [\App\Http\Controllers\OrdersController::class, "getByProfessor"]);
    Route::post('/create', [\App\Http\Controllers\OrdersController::class, "createOrder"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'professors',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/all_with_subjects', [\App\Http\Controllers\ProfessorsController::class, "getProfessors"]);
    Route::get('/get_by_subject_id', [\App\Http\Controllers\ProfessorsController::class, "getProfessorsById"]);
    Route::post('/short/all', [\App\Http\Controllers\ProfessorsController::class, "getShortProfessors"]);
    Route::post('/getSingle', [\App\Http\Controllers\ProfessorsController::class, "getSingle"]);
    Route::post('/page/all', [\App\Http\Controllers\ProfessorsController::class, "pageProfessorsList"]);
    Route::post('/positions/all', [\App\Http\Controllers\ProfessorsController::class, "positionsList"]);
    Route::post('/timetable', [\App\Http\Controllers\ProfessorsController::class, "getProfessorTimeTable"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'students',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/all', [\App\Http\Controllers\StudentsController::class, "getStudents"]);
    Route::post('/getSingle', [\App\Http\Controllers\StudentsController::class, "getSingle"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'subjects',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::get('/all', [\App\Http\Controllers\SubjectsController::class, "getSubjects"]);
    Route::post('/add', [\App\Http\Controllers\SubjectsController::class, "addSubjects"]);
    Route::post('/add/professor', [\App\Http\Controllers\SubjectsController::class, "addSubjectsProfessor"]);
    Route::delete('/delete/professor', [\App\Http\Controllers\SubjectsController::class, "deleteSubjectsProfessor"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'groups',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/all', [\App\Http\Controllers\GroupsController::class, "getGroups"]);
    Route::post('/add', [\App\Http\Controllers\GroupsController::class, "addGroup"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'specialities',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/all', [\App\Http\Controllers\SpecialitiesController::class, "getSpecialities"]);
    Route::post('/add', [\App\Http\Controllers\SpecialitiesController::class, "addSpeciality"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'other',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/statistic/orders', [\App\Http\Controllers\OthersController::class, "getStatistic"]);
    Route::post('/services', [\App\Http\Controllers\OthersController::class, "getServices"]);
    Route::post('/services/add', [\App\Http\Controllers\OthersController::class, "addService"]);
    Route::post('/faculties', [\App\Http\Controllers\OthersController::class, "getFaculties"]);
});
