<?php

use App\Http\Controllers\Academic\AcademicYearController;
use App\Http\Controllers\Class\RoomsController;
use App\Http\Controllers\Academic\SemesterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RolesController;
use App\Http\Controllers\Class\GradesController;
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
    // Roles
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RolesController::class, 'index'])->name('index');
        Route::get('/create', [RolesController::class, 'create'])->name('create');
        Route::post('/', [RolesController::class, 'store'])->name('store');
        Route::get('/{id}', [RolesController::class, 'show'])->name('show');
        Route::put('/{id}', [RolesController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle-status', [RolesController::class, 'toggleStatus'])->name('toggleStatus');
        Route::delete('/{id}', [RolesController::class, 'destroy'])->name('destroy');
    });

    // School-management
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

    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/roles', [UserController::class, 'roles'])->name('roles');
        Route::get('/schools', [UserController::class, 'schools'])->name('schools');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        Route::get('/{id}/detail', [UserController::class, 'detail'])->name('detail');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggleStatus');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    // school-admin section

    Route::prefix('academic-year')->name('academic-year.')->group(function () {
        Route::get('/', [AcademicYearController::class, 'index'])->name('index');
        Route::get('/create', [AcademicYearController::class, 'create'])->name('create');
        Route::post('/', [AcademicYearController::class, 'store'])->name('store');
        Route::get('/{id}', [AcademicYearController::class, 'show'])->name('show');
        Route::put('/{id}', [AcademicYearController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle-status', [AcademicYearController::class, 'toggleStatus'])->name('toggleStatus');
        Route::delete('/{id}', [AcademicYearController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('semesters')->name('semesters.')->group(function () {
        Route::get('/', [SemesterController::class, 'index'])->name('index');
        Route::get('/academic-years', [SemesterController::class, 'getAcademicYears'])->name('academic-years');
        Route::get('/create', [SemesterController::class, 'create'])->name('create');
        Route::post('/', [SemesterController::class, 'store'])->name('store');
        Route::get('/{id}', [SemesterController::class, 'show'])->name('show');
        Route::put('/{id}', [SemesterController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle-status', [SemesterController::class, 'toggleStatus'])->name('toggleStatus');
        Route::delete('/{id}', [SemesterController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('rooms')->name('rooms.')->group(function () {
        Route::get('/', [RoomsController::class, 'index'])->name('index');
        Route::get('/create', [RoomsController::class, 'create'])->name('create');
        Route::post('/', [RoomsController::class, 'store'])->name('store');
        Route::get('/{id}', [RoomsController::class, 'show'])->name('show');
        Route::put('/{id}', [RoomsController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle-status', [RoomsController::class, 'toggleStatus'])->name('toggleStatus');
        Route::delete('/{id}', [RoomsController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('grades')->name('grades.')->group(function () {
        Route::get('/rooms', [GradesController::class, 'getRooms'])->name('rooms');
        Route::get('/teachers', [GradesController::class, 'getTeachers'])->name('teachers');
        Route::get('/academic-years', [GradesController::class, 'getAcademicYears'])->name('academic-years');
        Route::get('/', [GradesController::class, 'index'])->name('index');
        Route::post('/', [GradesController::class, 'store'])->name('store');
        Route::get('/{id}', [GradesController::class, 'show'])->name('show');
        Route::put('/{id}', [GradesController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle-status', [GradesController::class, 'toggleStatus'])->name('toggleStatus');
        Route::delete('/{id}', [GradesController::class, 'destroy'])->name('destroy');
    });

});