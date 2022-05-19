<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\OthersController;
use App\Http\Controllers\ProfessorsController;
use App\Http\Controllers\SpecialitiesController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\SubjectsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'download',
    'namespace' => 'App\Http\Controllers',
], function () {
    Route::get('/treaty', [FilesController::class, "getTreaty"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth',
    'namespace' => 'App\Http\Controllers',
], function () {
    Route::post('/login', [AuthController::class, "login"]);
    Route::post('/registration', [AuthController::class, "registration"]);
    Route::post('/profileEdit', [AuthController::class, "profileEdit"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'user',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/me', [AuthController::class, "me"]);
    Route::post('/update/professor', [UserController::class, "updateProfessorData"]);
    Route::post('/update/student', [UserController::class, "updateStudentData"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'orders',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/all', [OrdersController::class, "getOrders"]);
    Route::post('/getSingle', [OrdersController::class, "getSingleOrder"]);
    Route::post('/professor/all', [OrdersController::class, "getByProfessor"]);
    Route::post('/create', [OrdersController::class, "createOrder"]);
    Route::post('/changeStatus', [OrdersController::class, "changeStatus"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'professors',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/all_with_subjects', [ProfessorsController::class, "getProfessors"]);
    Route::get('/get_by_subject_id', [ProfessorsController::class, "getProfessorsById"]);
    Route::post('/short/all', [ProfessorsController::class, "getShortProfessors"]);
    Route::post('/getSingle', [ProfessorsController::class, "getSingle"]);
    Route::post('/page/all', [ProfessorsController::class, "pageProfessorsList"]);
    Route::post('/positions/all', [ProfessorsController::class, "positionsList"]);
    Route::post('/timetable', [ProfessorsController::class, "getProfessorTimeTable"]);
    Route::post('/edit/description', [ProfessorsController::class, "editDescription"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'students',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/all', [StudentsController::class, "getStudents"]);
    Route::post('/getSingle', [StudentsController::class, "getSingle"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'subjects',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::get('/all', [SubjectsController::class, "getSubjects"]);
    Route::post('/add', [SubjectsController::class, "addSubjects"]);
    Route::post('/add/professor', [SubjectsController::class, "addSubjectsProfessor"]);
    Route::delete('/delete/professor', [SubjectsController::class, "deleteSubjectsProfessor"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'groups',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/all', [GroupsController::class, "getGroups"]);
    Route::post('/add', [GroupsController::class, "addGroup"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'specialities',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/all', [SpecialitiesController::class, "getSpecialities"]);
    Route::post('/add', [SpecialitiesController::class, "addSpeciality"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'other',
    'namespace' => 'App\Http\Controllers',
], function (){
    Route::post('/statistic/orders', [OthersController::class, "getStatistic"]);
    Route::post('/statistic/statements', [OthersController::class, "getStatement"]);
    Route::get('/statistic/statements', [OthersController::class, "getStatement"]);
    Route::post('/services', [OthersController::class, "getServices"]);
    Route::post('/services/add', [OthersController::class, "addService"]);
    Route::post('/faculties', [OthersController::class, "getFaculties"]);
});
