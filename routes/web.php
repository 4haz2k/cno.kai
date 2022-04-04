<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return "CNO KAI API";
});

Route::get('/users_list', function () {
    $users = \App\Models\User::all();
    $users_list = "";
    foreach ($users as $user){
        $users_list .= "<strong>{$user->login}</strong>"." | role: ". $user->role ."<br>";
    }

    return $users_list;
});

Route::get('/test', [\App\Http\Controllers\UserController::class, "test"]);
