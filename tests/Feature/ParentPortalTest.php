<?php

use App\Models\Core\User;
use App\Models\Student\StudentParent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    DB::table('school_profiles')->insert([
        'school_id' => 1,
        'school_name' => 'Sekolah Test',
        'school_type' => 'Senior High',
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('roles')->insert([
        'role_name' => 'student-parent',
        'role_description' => 'Parent of a student',
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

test('parent user can access parent portal dashboard', function () {
    $parentRoleId = DB::table('roles')->where('role_name', 'student-parent')->value('role_id');

    $userId = DB::table('users')->insertGetId([
        'name' => 'Test Parent User',
        'email' => 'testparent'.rand(100, 999).'@school.com',
        'password' => bcrypt('password'),
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $user = User::find($userId);

    DB::table('user_has_roles')->insert([
        'user_id' => $user->id,
        'role_id' => $parentRoleId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('user_has_schools')->insert([
        'user_id' => $user->id,
        'school_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $studentUserId = DB::table('users')->insertGetId([
        'name' => 'Anak Test User',
        'email' => 'anaktest'.rand(100, 999).'@school.com',
        'password' => bcrypt('password'),
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $studentId = DB::table('students')->insertGetId([
        'user_id' => $studentUserId,
        'nis' => '12345',
        'full_name' => 'Anak Test',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    StudentParent::create([
        'user_id' => $user->id,
        'student_id' => $studentId,
        'parent_name' => 'Bapak Test',
        'relationship' => 'father',
        'email' => $user->email,
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->get(route('parent.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Anak Test');
});

test('parent user can access attendance, scores, fees, reports, profile, messages and academic history', function () {
    $parentRoleId = DB::table('roles')->where('role_name', 'student-parent')->value('role_id');

    $userId = DB::table('users')->insertGetId([
        'name' => 'Test Parent User 2',
        'email' => 'testparent2'.rand(100, 999).'@school.com',
        'password' => bcrypt('password'),
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $user = User::find($userId);

    DB::table('user_has_roles')->insert([
        'user_id' => $user->id,
        'role_id' => $parentRoleId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('user_has_schools')->insert([
        'user_id' => $user->id,
        'school_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('parent.attendance'));
    $response->assertStatus(200);

    $response = $this->actingAs($user)->get(route('parent.scores'));
    $response->assertStatus(200);

    $response = $this->actingAs($user)->get(route('parent.fees'));
    $response->assertStatus(200);

    $response = $this->actingAs($user)->get(route('parent.reports'));
    $response->assertStatus(200);

    $response = $this->actingAs($user)->get(route('parent.profile'));
    $response->assertStatus(200);

    $response = $this->actingAs($user)->get(route('parent.messages'));
    $response->assertStatus(200);

    $response = $this->actingAs($user)->get(route('parent.academic-history'));
    $response->assertStatus(200);
});
