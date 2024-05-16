<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/user-lists', [HomeController::class, "getUserList"])->name('user.list');
Route::post('update-user', [HomeController::class, "postUpdateUser"])->name('user.update');
Route::post('/delete-user', [HomeController::class, "postDeleteUser"])->name('delete.user');
