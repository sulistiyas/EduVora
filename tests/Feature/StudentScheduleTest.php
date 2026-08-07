<?php

use App\Models\Core\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    DB::table('school_profiles')->insert([
        'school_id' => 1,
        'school_name' => 'SMA Negeri 1 Jakarta',
        'school_type' => 'Senior High',
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('roles')->insert([
        'role_name' => 'student',
        'role_description' => 'Siswa',
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

test('student user can access schedule page', function () {
    $roleId = DB::table('roles')->where('role_name', 'student')->value('role_id');

    $userId = DB::table('users')->insertGetId([
        'name' => 'Student Test',
        'email' => 'student.test@school.com',
        'password' => bcrypt('password'),
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $user = User::find($userId);

    DB::table('user_has_roles')->insert([
        'user_id' => $user->id,
        'role_id' => $roleId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('user_has_schools')->insert([
        'user_id' => $user->id,
        'school_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $academicYearId = DB::table('academic_years')->insertGetId([
        'school_id' => 1,
        'academic_year_name' => '2026/2027',
        'start_date' => now()->startOfYear(),
        'end_date' => now()->endOfYear(),
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $semesterId = DB::table('semesters')->insertGetId([
        'academic_year_id' => $academicYearId,
        'semester_name' => 'Semester Ganjil 2026/2027',
        'start_date' => now()->startOfYear(),
        'end_date' => now()->endOfYear(),
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $gradeId = DB::table('grades')->insertGetId([
        'school_id' => 1,
        'academic_year_id' => $academicYearId,
        'grade_name' => 'XTKJ 2',
        'level' => 10,
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('students')->insert([
        'user_id' => $user->id,
        'grade_id' => $gradeId,
        'nis' => '12345',
        'full_name' => 'Student Test',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $subjectId = DB::table('subjects')->insertGetId([
        'school_id' => 1,
        'subject_name' => 'Matematika',
        'subject_code' => 'MAT-10',
        'category' => 'Umum',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $gsId = DB::table('grade_subjects')->insertGetId([
        'grade_id' => $gradeId,
        'subject_id' => $subjectId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('schedules')->insert([
        'school_id' => 1,
        'grade_subject_id' => $gsId,
        'semester_id' => $semesterId,
        'day_of_week' => 1,
        'start_time' => '07:00:00',
        'end_time' => '08:30:00',
        'session_type' => 'regular',
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('student.schedule'));

    $response->assertStatus(200);
    $response->assertSee('Jadwal Pelajaran');
    $response->assertSee('XTKJ 2');
    $response->assertSee('Matematika');
});
