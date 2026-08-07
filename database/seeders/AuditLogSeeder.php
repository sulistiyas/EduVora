<?php

namespace Database\Seeders;

use App\Models\Core\AuditLog;
use App\Models\Core\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            return;
        }

        $superAdmin = $users->firstWhere('email', 'superadmin@example.com') ?? $users->first();
        $admin1 = $users->firstWhere('email', 'admin1@school.com') ?? $users->skip(1)->first() ?? $superAdmin;
        $admin2 = $users->firstWhere('email', 'admin2@school.com') ?? $superAdmin;
        $teacher1 = $users->firstWhere('email', 'teacher1@school.com') ?? $superAdmin;
        $teacher2 = $users->firstWhere('email', 'teacher2@school.com') ?? $superAdmin;

        $logs = [
            [
                'user_id' => $superAdmin->id,
                'action' => 'Menambahkan sekolah baru SMA N 1 Jakarta',
                'table_name' => 'school_profiles',
                'record_id' => 1,
                'old_values' => null,
                'new_values' => json_encode(['school_name' => 'SMA N 1 Jakarta', 'school_type' => 'Senior High']),
                'ip_address' => '192.168.1.10',
                'created_at' => Carbon::now()->subMinutes(5),
            ],
            [
                'user_id' => $admin1->id,
                'action' => 'Mengubah status akun pengguna',
                'table_name' => 'users',
                'record_id' => 12,
                'old_values' => json_encode(['status' => 'inactive']),
                'new_values' => json_encode(['status' => 'active']),
                'ip_address' => '192.168.1.15',
                'created_at' => Carbon::now()->subMinutes(18),
            ],
            [
                'user_id' => $superAdmin->id,
                'action' => 'Memperbarui pengaturan platform EduVora',
                'table_name' => 'platform_settings',
                'record_id' => 1,
                'old_values' => json_encode(['app_name' => 'EduVora System']),
                'new_values' => json_encode(['app_name' => 'EduSaaS Management']),
                'ip_address' => '192.168.1.10',
                'created_at' => Carbon::now()->subMinutes(42),
            ],
            [
                'user_id' => $teacher1->id,
                'action' => 'Mengunggah jadwal pelajaran Semester Ganjil',
                'table_name' => 'schedules',
                'record_id' => 5,
                'old_values' => null,
                'new_values' => json_encode(['subject' => 'Matematika', 'day' => 'Senin']),
                'ip_address' => '192.168.1.22',
                'created_at' => Carbon::now()->subHours(1)->subMinutes(15),
            ],
            [
                'user_id' => $admin2->id,
                'action' => 'Menambahkan data guru baru',
                'table_name' => 'teachers',
                'record_id' => 8,
                'old_values' => null,
                'new_values' => json_encode(['teacher_name' => 'Budi Santoso, S.Pd']),
                'ip_address' => '192.168.1.18',
                'created_at' => Carbon::now()->subHours(2)->subMinutes(30),
            ],
            [
                'user_id' => $superAdmin->id,
                'action' => 'Mengubah hak akses role Admin Sekolah',
                'table_name' => 'roles',
                'record_id' => 2,
                'old_values' => json_encode(['permissions' => ['read']]),
                'new_values' => json_encode(['permissions' => ['read', 'write', 'delete']]),
                'ip_address' => '192.168.1.10',
                'created_at' => Carbon::now()->subHours(3)->subMinutes(10),
            ],
            [
                'user_id' => $admin1->id,
                'action' => 'Menambahkan data siswa baru',
                'table_name' => 'students',
                'record_id' => 25,
                'old_values' => null,
                'new_values' => json_encode(['student_name' => 'Ahmad Rizky', 'nisn' => '0054321098']),
                'ip_address' => '192.168.1.15',
                'created_at' => Carbon::now()->subHours(4)->subMinutes(45),
            ],
            [
                'user_id' => $superAdmin->id,
                'action' => 'Mengatur tahun akademik 2026/2027',
                'table_name' => 'academic_years',
                'record_id' => 1,
                'old_values' => json_encode(['is_active' => false]),
                'new_values' => json_encode(['is_active' => true]),
                'ip_address' => '192.168.1.10',
                'created_at' => Carbon::now()->subHours(6),
            ],
            [
                'user_id' => $teacher2->id,
                'action' => 'Membuat ruang kelas baru XI IPA 1',
                'table_name' => 'rooms',
                'record_id' => 4,
                'old_values' => null,
                'new_values' => json_encode(['room_name' => 'Lab Komputer 1']),
                'ip_address' => '192.168.1.30',
                'created_at' => Carbon::now()->subHours(8)->subMinutes(20),
            ],
            [
                'user_id' => $admin2->id,
                'action' => 'Memperbarui data profil pengguna',
                'table_name' => 'users',
                'record_id' => 4,
                'old_values' => json_encode(['phone_number' => '081100000000']),
                'new_values' => json_encode(['phone_number' => '081234567890']),
                'ip_address' => '192.168.1.18',
                'created_at' => Carbon::now()->subHours(12),
            ],
            [
                'user_id' => $superAdmin->id,
                'action' => 'Menghapus data mata pelajaran pilihan',
                'table_name' => 'subjects',
                'record_id' => 12,
                'old_values' => json_encode(['subject_name' => 'Bahasa Daerah (Obsolete)']),
                'new_values' => null,
                'ip_address' => '192.168.1.10',
                'created_at' => Carbon::now()->subDays(1),
            ],
            [
                'user_id' => $admin1->id,
                'action' => 'Membuat tagihan pembayaran SPP',
                'table_name' => 'student_invoices',
                'record_id' => 101,
                'old_values' => null,
                'new_values' => json_encode(['amount' => 500000, 'month' => 'Agustus']),
                'ip_address' => '192.168.1.15',
                'created_at' => Carbon::now()->subDays(1)->subHours(4),
            ],
        ];

        foreach ($logs as $log) {
            $log['updated_at'] = $log['created_at'];
            AuditLog::create($log);
        }
    }
}
