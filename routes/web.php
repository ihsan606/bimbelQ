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
    return view('welcome');
});

Route::resource('/users', \App\Http\Controllers\UserController::class);

Route::resource('/class', \App\Http\Controllers\ClassController::class);

Route::resource('/mapels', \App\Http\Controllers\MapelController::class);

Route::resource('/siswas', \App\Http\Controllers\MapelController::class);

Route::resource('/tentors', \App\Http\Controllers\MapelController::class);


Route::get('/owner', [\App\Http\Controllers\OwnerController::class, 'index']);

