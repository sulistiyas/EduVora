<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;


// Auth
Route::get('/', fn () => redirect()->route('login'))->name('home');
Route::get('/login', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// middleware auth
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('pages.dash.index');
    })->name('dashboard');

    Route::prefix('users')->group(function () {
        Route::get('/', [\App\Http\Controllers\UserController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\UserController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\UserController::class, 'show'])->name('show');
        Route::put('/{id}', [\App\Http\Controllers\UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('destroy');
    });
});