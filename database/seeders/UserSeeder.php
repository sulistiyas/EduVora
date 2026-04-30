<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 🔑 Ambil role dari DB
        $roles = DB::table('roles')->pluck('role_id', 'role_name');

        $userRolePivot = [];

        // =========================
        // SUPER ADMIN (1)
        // =========================
        $userId = $this->insertUser('Super Admin', 'superadmin@example.com', '081100000001', $now);
        $userRolePivot[] = $this->attachRole($userId, $roles['super-admin'], $now);

        // =========================
        // ADMIN SCHOOL (4)
        // =========================
        for ($i = 1; $i <= 4; $i++) {
            $userId = $this->insertUser(
                "Admin School {$i}",
                "admin{$i}@school.com",
                "08110000010{$i}",
                $now
            );
            $userRolePivot[] = $this->attachRole($userId, $roles['school-admin'], $now);
        }

        // =========================
        // HEADMASTER (4)
        // =========================
        for ($i = 1; $i <= 4; $i++) {
            $userId = $this->insertUser(
                "Headmaster {$i}",
                "headmaster{$i}@school.com",
                "08110000020{$i}",
                $now
            );
            $userRolePivot[] = $this->attachRole($userId, $roles['headmaster'], $now);
        }

        // =========================
        // TEACHER (10)
        // =========================
        for ($i = 1; $i <= 10; $i++) {
            $userId = $this->insertUser(
                "Teacher {$i}",
                "teacher{$i}@school.com",
                "08110000030{$i}",
                $now
            );
            $userRolePivot[] = $this->attachRole($userId, $roles['teacher'], $now);
        }

        // =========================
        // STUDENT (50)
        // =========================
        for ($i = 1; $i <= 50; $i++) {
            $userId = $this->insertUser(
                "Student {$i}",
                "student{$i}@school.com",
                "08110000040{$i}",
                $now
            );
            $userRolePivot[] = $this->attachRole($userId, $roles['student'], $now);
        }

        // =========================
        // STUDENT PARENT (50)
        // =========================
        for ($i = 1; $i <= 50; $i++) {
            $userId = $this->insertUser(
                "Parent {$i}",
                "parent{$i}@school.com",
                "08110000050{$i}",
                $now
            );
            $userRolePivot[] = $this->attachRole($userId, $roles['student-parent'], $now);
        }

        // 🔥 Insert pivot sekaligus (lebih cepat)
        DB::table('user_has_roles')->insert($userRolePivot);
    }

    private function insertUser($name, $email, $phone, $now)
    {
        return DB::table('users')->insertGetId([
            'name' => $name,
            'email' => $email,
            'email_verified_at' => $now,
            'password' => Hash::make('password123'),
            'phone_number' => $phone,
            'status' => Arr::random(['active', 'inactive']),
            'profile_picture' => null,
            'remember_token' => Str::random(10),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function attachRole($userId, $roleId, $now)
    {
        return [
            'user_id' => $userId,
            'role_id' => $roleId,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}