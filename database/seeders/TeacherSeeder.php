<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $teachers = DB::table('users')
            ->join('user_has_roles', 'users.id', '=', 'user_has_roles.user_id')
            ->join('roles', 'roles.role_id', '=', 'user_has_roles.role_id')
            ->where('roles.role_name', 'teacher')
            ->select('users.*')
            ->get();

        $data = [];

        foreach ($teachers as $i => $user) {
            $data[] = [
                'user_id' => $user->id,
                'nip' => 'NIP'.str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'nik' => '3174'.rand(1000000000, 9999999999),
                'full_name' => $user->name,
                'birth_place' => 'Jakarta',
                'birth_date' => now()->subYears(rand(25, 50)),
                'gender' => rand(0, 1) ? 'male' : 'female',
                'religion' => 'Islam',
                'address' => 'Alamat '.$user->name,
                'phone' => $user->phone_number,
                'email' => $user->email,
                'employment_status' => rand(0, 1) ? 'permanent' : 'contract',
                'position' => 'Guru',
                'grade_level' => 'III/a',
                'education_level' => 'bachelor',
                'major' => 'Pendidikan',
                'certification' => 'Sertifikasi Guru',
                'npwp' => rand(1000000000, 9999999999),
                'join_date' => now()->subYears(rand(1, 10)),
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('teachers')->insert($data);
    }
}
