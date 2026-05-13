<?php

use App\Http\Controllers\Academic\AcademicYearController;
use App\Http\Controllers\Academic\ScheduleController;
use App\Http\Controllers\Academic\SemesterController;
use App\Http\Controllers\Academic\SubjectController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RolesController;
use App\Http\Controllers\Class\GradesController;
use App\Http\Controllers\Class\GradeSubjectsController;
use App\Http\Controllers\Class\RoomsController;
use App\Http\Controllers\School\StudentController;
use App\Http\Controllers\School\TeacherController;
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
        Route::get('/subjects', [GradesController::class, 'getSubjects'])->name('subjects');
        
        Route::get('/', [GradesController::class, 'index'])->name('index');
        Route::post('/', [GradesController::class, 'store'])->name('store');

        Route::get('/{id}/detail', [GradesController::class, 'detail'])->name('detail');
        Route::get('/{id}', [GradesController::class, 'show'])->name('show');
        Route::put('/{id}', [GradesController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle-status', [GradesController::class, 'toggleStatus'])->name('toggleStatus');
        Route::delete('/{id}', [GradesController::class, 'destroy'])->name('destroy');

        Route::prefix('{gradeId}/subjects')->name('grade-subjects.')->group(function () {
            Route::get('/',         [GradeSubjectsController::class, 'index'])->name('index');
            Route::post('/',        [GradeSubjectsController::class, 'store'])->name('store');
            Route::patch('/{id}',   [GradeSubjectsController::class, 'update'])->name('update');
            Route::delete('/{id}',  [GradeSubjectsController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('subjects')->name('subjects.')->group(function () {
        Route::get('/', [SubjectController::class, 'index'])->name('index');
        Route::get('/create', [SubjectController::class, 'create'])->name('create');
        Route::post('/', [SubjectController::class, 'store'])->name('store');
        Route::get('/{id}', [SubjectController::class, 'show'])->name('show');
        Route::put('/{id}', [SubjectController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle-status', [SubjectController::class, 'toggleStatus'])->name('toggleStatus');
        Route::delete('/{id}', [SubjectController::class, 'destroy'])->name('destroy');
    });

    // Schedules

    Route::prefix('schedules')->name('schedules.')->group(function () {
 
        // Dropdown data (harus di atas resource agar tidak bentrok dengan {id})
        Route::get('semesters',[ScheduleController::class, 'semesters'])    ->name('semesters');
        Route::get('rooms',[ScheduleController::class, 'rooms'])        ->name('rooms');
        Route::get('grade-subjects',[ScheduleController::class, 'gradeSubjects'])->name('grade-subjects');
    
        // Toggle status
        Route::patch('{id}/toggle-status', [ScheduleController::class, 'toggleStatus'])->name('toggle-status');
    
        // CRUD
        Route::get('/', [ScheduleController::class, 'index'])  ->name('index');
        Route::post('/', [ScheduleController::class, 'store'])  ->name('store');
        Route::get('/{id}', [ScheduleController::class, 'show'])   ->name('show');
        Route::put('/{id}', [ScheduleController::class, 'update']) ->name('update');
        Route::delete('/{id}', [ScheduleController::class, 'destroy'])->name('destroy');
    });

    // Users and Teacher for school
    Route::prefix('school-admin')->name('school-admin.')->group(function () {
        // Student
        Route::prefix('students')->name('students.')->group(function () {
            // Dropdown helpers (harus di atas /{id})
            Route::get('/class-groups', [StudentController::class, 'classGroups'])->name('class-groups');
    
            // CRUD
            Route::get('/', [StudentController::class, 'index'])->name('index');
            Route::get('/create', [StudentController::class, 'create'])->name('create');
            Route::post('/', [StudentController::class, 'store'])->name('store');
            Route::get('/{id}', [StudentController::class, 'show'])->name('show');
            Route::get('/{id}/detail', [StudentController::class, 'detail'])->name('detail');
            Route::put('/{id}', [StudentController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle-status', [StudentController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('/{id}', [StudentController::class, 'destroy'])->name('destroy');
        });

        // Teacher
        Route::prefix('teachers')->name('teachers.')->group(function () {
            // Helper dropdown (harus di atas /{id})
            Route::get('/employment-statuses', [TeacherController::class, 'employmentStatuses'])->name('employment-statuses');
    
            // CRUD
            Route::get('/', [TeacherController::class, 'index'])->name('index');
            Route::get('/create', [TeacherController::class, 'create'])->name('create');
            Route::post('/', [TeacherController::class, 'store'])->name('store');
            Route::get('/{id}', [TeacherController::class, 'show'])->name('show');
            Route::get('/{id}/detail', [TeacherController::class, 'detail'])->name('detail');
            Route::put('/{id}', [TeacherController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle-status', [TeacherController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('/{id}', [TeacherController::class, 'destroy'])->name('destroy');
        });
    });

});