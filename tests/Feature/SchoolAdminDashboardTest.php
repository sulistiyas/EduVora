<?php

use App\Models\Core\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    DB::table('school_profiles')->insert([
        'school_id' => 1,
        'school_name' => 'Sekolah Test School Admin',
        'school_type' => 'Senior High',
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('roles')->insert([
        'role_name' => 'school-admin',
        'role_description' => 'Admin Sekolah',
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

test('school admin user can access school admin dashboard without error', function () {
    $roleId = DB::table('roles')->where('role_name', 'school-admin')->value('role_id');

    $userId = DB::table('users')->insertGetId([
        'name' => 'Admin Sekolah Test',
        'email' => 'adminsek'.rand(100, 999).'@school.com',
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
        'semester_name' => 'Ganjil',
        'start_date' => now()->startOfYear(),
        'end_date' => now()->endOfYear(),
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $gradeId = DB::table('grades')->insertGetId([
        'school_id' => 1,
        'academic_year_id' => $academicYearId,
        'grade_name' => 'X IPA 1',
        'level' => 10,
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('assigments')->insert([
        'title' => 'Tugas Matematika 1',
        'grade_id' => $gradeId,
        'assigned_date' => now(),
        'due_date' => now()->addDays(7),
        'status' => 'published',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('school-admin.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Dashboard Admin Sekolah');
    $response->assertSee('Sekolah Test School Admin');
    $response->assertSee('Tugas Aktif');
});
