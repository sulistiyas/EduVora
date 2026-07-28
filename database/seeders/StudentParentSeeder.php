<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentParentSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $parentRoleId = DB::table('roles')->where('role_name', 'student-parent')->value('role_id');
        $parentUserIds = DB::table('user_has_roles')
            ->where('role_id', $parentRoleId)
            ->pluck('user_id')
            ->toArray();

        $students = DB::table('students')->get();

        $data = [];

        foreach ($students as $index => $student) {
            $userId = ! empty($parentUserIds) ? $parentUserIds[$index % count($parentUserIds)] : null;

            $data[] = [
                'user_id' => $userId,
                'student_id' => $student->id,
                'parent_name' => 'Orang Tua '.$student->full_name,
                'relationship' => ['father', 'mother'][rand(0, 1)],
                'email' => $userId ? DB::table('users')->where('id', $userId)->value('email') : null,
                'phone_number' => '08'.rand(1000000000, 9999999999),
                'occupation' => 'Wiraswasta',
                'address' => 'Alamat orang tua',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('student_parents')->insert($data);
    }
}
