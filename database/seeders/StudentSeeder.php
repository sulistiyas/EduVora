<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ambil user student + school
        $students = DB::table('users')
            ->join('user_has_roles', 'users.id', '=', 'user_has_roles.user_id')
            ->join('roles', 'roles.role_id', '=', 'user_has_roles.role_id')
            ->where('roles.role_name', 'student')
            ->select('users.*')
            ->get();

        $data = [];

        foreach ($students as $i => $user) {
            $data[] = [
                'user_id' => $user->id,
                'nis' => 'NIS' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'full_name' => $user->name,
                'nick_name' => explode(' ', $user->name)[0],
                'email' => $user->email,
                'birth_date' => now()->subYears(rand(12, 18)),
                'gender' => rand(0,1) ? 'male' : 'female',
                'phone_number' => $user->phone_number,
                'address' => 'Alamat ' . $user->name,
                'city' => 'City',
                'province' => 'Province',
                'postal_code' => '12345',
                'profile_photo' => null,
                'grade_id' => null,
                'class_group' => 'A',
                'status' => 'active',
                'enrollment_date' => now()->subYears(rand(1, 3)),
                'graduation_date' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('students')->insert($data);
    }
}