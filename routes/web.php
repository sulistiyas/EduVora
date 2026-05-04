<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RolesController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\UserController;
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

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RolesController::class, 'index'])->name('index');
        Route::get('/create', [RolesController::class, 'create'])->name('create');
        Route::post('/', [RolesController::class, 'store'])->name('store');
        Route::get('/{id}', [RolesController::class, 'show'])->name('show');
        Route::put('/{id}', [RolesController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle-status', [RolesController::class, 'toggleStatus'])->name('toggleStatus');
        Route::delete('/{id}', [RolesController::class, 'destroy'])->name('destroy');
    });
    
    Route::prefix('school-management')->name('school-management.')->group(function (){
        Route::get('/',[SchoolController::class, 'index'])->name('index');
        Route::get('/create', [SchoolController::class, 'create'])->name('create');
        Route::post('/', [SchoolController::class, 'store'])->name('store');
        Route::get('/{id}', [SchoolController::class, 'show'])->name('show');
        Route::get('/{id}/detail', [SchoolController::class, 'detail'])->name('detail');
        Route::put('/{id}', [SchoolController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle-status', [SchoolController::class, 'toggleStatus'])->name('toggleStatus');
        Route::delete('/{id}', [SchoolController::class, 'destroy'])->name('destroy');
    });
});