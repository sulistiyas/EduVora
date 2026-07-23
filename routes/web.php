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
use App\Http\Controllers\School\StudentScoreController;
use App\Http\Controllers\School\TeacherController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\Reports\AttendanceReportController;
use App\Http\Controllers\Teacher\Reports\ScoreReportController;
use App\Http\Controllers\Teacher\ScheduleController as TeacherScheduleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ─── Public ───────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('login'))->name('home');
Route::get('/login',  [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');


// ─── Authenticated (semua role) ───────────────────────────
Route::middleware(['auth'])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD — role-based redirect
    |--------------------------------------------------------------------------
    | Route /dashboard dipakai sebagai "hub" universal.
    | Setiap role diarahkan ke view/controller masing-masing.
    | Gunakan named route 'dashboard' di seluruh aplikasi (misal: setelah login,
    | notifikasi, breadcrumb) — redirect otomatis sesuai role.
    */
    Route::get('/dashboard', function () {
        $user = Auth::user();

        $role = $user->role_name ?? 'guest';

        return match ($role) {
            'super-admin'  => redirect()->route('super-admin.dashboard'),
            'school-admin' => redirect()->route('school-admin.dashboard'),
            'teacher'      => redirect()->route('teacher.dashboard'),
            'student'      => redirect()->route('student.dashboard'),
            default        => abort(403, 'Role tidak dikenali.'),
        };
    })->name('dashboard');


    // ── SUPER-ADMIN ──────────────────────────────────────
    Route::middleware(['role:super-admin'])->group(function () {

        Route::get('/super-admin/dashboard', fn () => view('pages.dash.index'))
            ->name('super-admin.dashboard');

        // Roles & Permissions
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/',                    [RolesController::class, 'index'])->name('index');
            Route::post('/',                   [RolesController::class, 'store'])->name('store');
            Route::get('/{id}',                [RolesController::class, 'show'])->name('show');
            Route::put('/{id}',                [RolesController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle-status',[RolesController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('/{id}',             [RolesController::class, 'destroy'])->name('destroy');
        });

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/',                    [UserController::class, 'index'])->name('index');
            Route::get('/create',              [UserController::class, 'create'])->name('create');
            Route::post('/',                   [UserController::class, 'store'])->name('store');
            Route::get('/roles',               [UserController::class, 'roles'])->name('roles');
            Route::get('/schools',             [UserController::class, 'schools'])->name('schools');
            Route::get('/{id}',                [UserController::class, 'show'])->name('show');
            Route::get('/{id}/detail',         [UserController::class, 'detail'])->name('detail');
            Route::put('/{id}',                [UserController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle-status',[UserController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('/{id}',             [UserController::class, 'destroy'])->name('destroy');
        });

        // School Management
        Route::prefix('school-management')->name('school-management.')->group(function () {
            Route::get('/',                    [SchoolController::class, 'index'])->name('index');
            Route::post('/',                   [SchoolController::class, 'store'])->name('store');
            Route::get('/{id}',                [SchoolController::class, 'show'])->name('show');
            Route::get('/{id}/detail',         [SchoolController::class, 'detail'])->name('detail');
            Route::put('/{id}',                [SchoolController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle-status',[SchoolController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('/{id}',             [SchoolController::class, 'destroy'])->name('destroy');
        });

    });


    // ── SCHOOL-ADMIN ─────────────────────────────────────
    Route::middleware(['role:super-admin,school-admin'])->group(function () {

        Route::get('/school-admin/dashboard', fn () => view('pages.dash.index'))
            ->name('school-admin.dashboard');

        // Academic Year
        Route::prefix('academic-year')->name('academic-year.')->group(function () {
            Route::get('/',                    [AcademicYearController::class, 'index'])->name('index');
            Route::post('/',                   [AcademicYearController::class, 'store'])->name('store');
            Route::get('/{id}',                [AcademicYearController::class, 'show'])->name('show');
            Route::put('/{id}',                [AcademicYearController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle-status',[AcademicYearController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('/{id}',             [AcademicYearController::class, 'destroy'])->name('destroy');
        });

        // Semesters
        Route::prefix('semesters')->name('semesters.')->group(function () {
            Route::get('/',                    [SemesterController::class, 'index'])->name('index');
            Route::get('/academic-years',      [SemesterController::class, 'getAcademicYears'])->name('academic-years');
            Route::post('/',                   [SemesterController::class, 'store'])->name('store');
            Route::get('/{id}',                [SemesterController::class, 'show'])->name('show');
            Route::put('/{id}',                [SemesterController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle-status',[SemesterController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('/{id}',             [SemesterController::class, 'destroy'])->name('destroy');
        });

        // Rooms
        Route::prefix('rooms')->name('rooms.')->group(function () {
            Route::get('/',                    [RoomsController::class, 'index'])->name('index');
            Route::post('/',                   [RoomsController::class, 'store'])->name('store');
            Route::get('/{id}',                [RoomsController::class, 'show'])->name('show');
            Route::put('/{id}',                [RoomsController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle-status',[RoomsController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('/{id}',             [RoomsController::class, 'destroy'])->name('destroy');
        });

        // Grades (Kelas)
        Route::prefix('grades')->name('grades.')->group(function () {
            Route::get('/rooms',               [GradesController::class, 'getRooms'])->name('rooms');
            Route::get('/teachers',            [GradesController::class, 'getTeachers'])->name('teachers');
            Route::get('/academic-years',      [GradesController::class, 'getAcademicYears'])->name('academic-years');
            Route::get('/subjects',            [GradesController::class, 'getSubjects'])->name('subjects');

            Route::get('/',                    [GradesController::class, 'index'])->name('index');
            Route::post('/',                   [GradesController::class, 'store'])->name('store');
            Route::get('/{id}/detail',         [GradesController::class, 'detail'])->name('detail');
            Route::get('/{id}',                [GradesController::class, 'show'])->name('show');
            Route::put('/{id}',                [GradesController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle-status',[GradesController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('/{id}',             [GradesController::class, 'destroy'])->name('destroy');

            Route::prefix('{gradeId}/subjects')->name('grade-subjects.')->group(function () {
                Route::get('/',       [GradeSubjectsController::class, 'index'])->name('index');
                Route::post('/',      [GradeSubjectsController::class, 'store'])->name('store');
                Route::patch('/{id}', [GradeSubjectsController::class, 'update'])->name('update');
                Route::delete('/{id}',[GradeSubjectsController::class, 'destroy'])->name('destroy');
            });

            Route::get('/students/list', [
                GradesController::class,
                'getStudents'
            ]);

            Route::post('/{gradeId}/assign-students', [
                GradesController::class,
                'assignStudents'
            ]);

            Route::delete('/{gradeId}/students/{studentId}', [
                GradesController::class,
                'removeStudent'
            ]);

            Route::delete('/{gradeId}/students', [
                GradesController::class,
                'clearStudents'
            ]);
        });

        // Subjects
        Route::prefix('subjects')->name('subjects.')->group(function () {
            Route::get('/',                    [SubjectController::class, 'index'])->name('index');
            Route::post('/',                   [SubjectController::class, 'store'])->name('store');
            Route::get('/{id}',                [SubjectController::class, 'show'])->name('show');
            Route::put('/{id}',                [SubjectController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle-status',[SubjectController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('/{id}',             [SubjectController::class, 'destroy'])->name('destroy');
        });

        // Schedules
        Route::prefix('schedules')->name('schedules.')->group(function () {
            Route::get('/semesters',           [ScheduleController::class, 'semesters'])->name('semesters');
            Route::get('/rooms',               [ScheduleController::class, 'rooms'])->name('rooms');
            Route::get('/grade-subjects',      [ScheduleController::class, 'gradeSubjects'])->name('grade-subjects');
            Route::patch('/{id}/toggle-status',[ScheduleController::class, 'toggleStatus'])->name('toggle-status');

            Route::get('/',                    [ScheduleController::class, 'index'])->name('index');
            Route::post('/',                   [ScheduleController::class, 'store'])->name('store');
            Route::get('/{id}',                [ScheduleController::class, 'show'])->name('show');
            Route::put('/{id}',                [ScheduleController::class, 'update'])->name('update');
            Route::delete('/{id}',             [ScheduleController::class, 'destroy'])->name('destroy');
        });

        // Students & Teachers
        Route::prefix('school-admin')->name('school-admin.')->group(function () {

            Route::prefix('students')->name('students.')->group(function () {
                Route::get('/class-groups',        [StudentController::class, 'classGroups'])->name('class-groups');
                Route::get('/',                    [StudentController::class, 'index'])->name('index');
                Route::get('/create',              [StudentController::class, 'create'])->name('create');
                Route::post('/',                   [StudentController::class, 'store'])->name('store');
                Route::get('/{id}',                [StudentController::class, 'show'])->name('show');
                Route::get('/{id}/detail',         [StudentController::class, 'detail'])->name('detail');
                Route::put('/{id}',                [StudentController::class, 'update'])->name('update');
                Route::patch('/{id}/toggle-status',[StudentController::class, 'toggleStatus'])->name('toggleStatus');
                Route::delete('/{id}',             [StudentController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('teachers')->name('teachers.')->group(function () {
                Route::get('/employment-statuses', [TeacherController::class, 'employmentStatuses'])->name('employment-statuses');
                Route::get('/',                    [TeacherController::class, 'index'])->name('index');
                Route::get('/create',              [TeacherController::class, 'create'])->name('create');
                Route::post('/',                   [TeacherController::class, 'store'])->name('store');
                Route::get('/{id}',                [TeacherController::class, 'show'])->name('show');
                Route::get('/{id}/detail',         [TeacherController::class, 'detail'])->name('detail');
                Route::put('/{id}',                [TeacherController::class, 'update'])->name('update');
                Route::patch('/{id}/toggle-status',[TeacherController::class, 'toggleStatus'])->name('toggleStatus');
                Route::delete('/{id}',             [TeacherController::class, 'destroy'])->name('destroy');
                
            });
        });

        // Student Scores
        Route::prefix('student-scores')->name('student-scores.')->group(function () {
            Route::get('/', [StudentScoreController::class, 'index'])->name('index');
            Route::post('/', [StudentScoreController::class, 'store'])->name('store');
            Route::get('/{id}', [StudentScoreController::class, 'show'])->name('show');
            Route::put('/{id}', [StudentScoreController::class, 'update'])->name('update');
            Route::delete('/{id}', [StudentScoreController::class, 'destroy'])->name('destroy');
        });

    });


    // ── TEACHER ──────────────────────────────────────────
    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {

        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])
            ->name('dashboard');

        Route::prefix('schedules')->name('schedules.')->group(function () {
            Route::get('/',        [TeacherScheduleController::class, 'index'])->name('index');
            Route::get('/semesters', [TeacherScheduleController::class, 'semesters'])->name('semesters');
            Route::get('/{id}',    [TeacherScheduleController::class, 'show'])->name('show');
            // Tidak ada: store, update, destroy, toggle-status, rooms, grade-subjects
        });

        Route::prefix('attendance')->name('attendance.')->group(function () {
 
            // Dropdown helpers
            Route::get('/semesters', [AttendanceController::class, 'semesters'])->name('semesters');
        
            // Entry point dari halaman Jadwal — auto create/resume session
            Route::post('/start',     [AttendanceController::class, 'start'])->name('start');
        
            // Index: list semua session milik teacher
            Route::get('/',          [AttendanceController::class, 'index'])->name('index');

            // Route::get ('attendance/create',      [AttendanceController::class, 'create'])->name('create');
            Route::post('/',             [AttendanceController::class, 'store']) ->name('store');
        
            // Show: detail form absensi per siswa
            Route::get('/{id}',      [AttendanceController::class, 'show'])->name('show');
        
            // Save details (auto-save, bisa dipanggil berkali-kali)
            Route::patch('/{id}/details', [AttendanceController::class, 'saveDetails'])->name('save-details');
        
            // Submit (draft → submitted)
            Route::patch('/{id}/submit',  [AttendanceController::class, 'submit'])->name('submit');
        
            // Lock / Unlock
            Route::patch('/{id}/lock',    [AttendanceController::class, 'lock'])->name('lock');
            Route::patch('/{id}/unlock',  [AttendanceController::class, 'unlock'])->name('unlock');
        
        });

        // ── Scores ────────────────────────────────────────────────────────────────
        Route::prefix('scores')->name('scores.')->group(function () {
    
            // Index — list all score sessions
            Route::get('/',           [StudentScoreController::class, 'index'])->name('index');
    
            // Store — create new score session (POST from modal)
            Route::post('/',          [StudentScoreController::class, 'store'])->name('store');

            // Dropdown helpers
            Route::get('/data/semesters',     [StudentScoreController::class, 'semesters'])->name('semesters');
            Route::get('/data/grade-subjects',[StudentScoreController::class, 'gradeSubjects'])->name('grade-subjects');
    
            // Show — detail / input nilai per siswa
            Route::get('/{id}',       [StudentScoreController::class, 'show'])->name('show');
    
            // Save details — PATCH bulk upsert scores
            Route::patch('/{id}/details',  [StudentScoreController::class, 'saveDetails'])->name('save-details');
    
            // Toggle publish / unpublish
            Route::patch('/{id}/publish',  [StudentScoreController::class, 'togglePublish'])->name('publish');
    
            // Update session metadata
            Route::patch('/{id}',     [StudentScoreController::class, 'update'])->name('update');
    
            // Delete session
            Route::delete('/{id}',    [StudentScoreController::class, 'destroy'])->name('destroy');
        });

        Route::get('/grade-subjects', [
            StudentScoreController::class,
            'gradeSubjects'
        ])->name('grade-subjects');

        Route::prefix('reports/attendance')->name('reports.attendance.')->group(function () {
            Route::get('/',          [AttendanceReportController::class, 'index'])->name('index');
            Route::get('/export',    [AttendanceReportController::class, 'export'])->name('export');
            Route::get('/{session}', [AttendanceReportController::class, 'show'])->name('show');
        });

        Route::prefix('reports/score')->name('reports.score.')->group(function(){
            Route::get('/',          [ScoreReportController::class, 'index'])->name('index');
            Route::get('/export',    [ScoreReportController::class, 'export'])->name('export');
            Route::get('/{session}', [ScoreReportController::class, 'show'])->name('show');
        });
    });


    // ── STUDENT ──────────────────────────────────────────
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])
            ->name('dashboard');
        
    });

});