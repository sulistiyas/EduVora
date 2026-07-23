<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $roles = [
            [
                'role_name' => 'super-admin',
                'role_description' => 'Super Administrator with full access',
                'status' => 'active',
            ],
            [
                'role_name' => 'school-admin',
                'role_description' => 'Administrator of a school',
                'status' => 'active',
            ],
            [
                'role_name' => 'headmaster',
                'role_description' => 'Headmaster / Principal',
                'status' => 'active',
            ],
            [
                'role_name' => 'teacher',
                'role_description' => 'Teacher role',
                'status' => 'active',
            ],
            [
                'role_name' => 'student',
                'role_description' => 'Student role',
                'status' => 'active',
            ],
            [
                'role_name' => 'student-parent',
                'role_description' => 'Parent of a student',
                'status' => 'active',
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['role_name' => $role['role_name']],
                [
                    'role_description' => $role['role_description'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
