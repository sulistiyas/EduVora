<?php

namespace Database\Seeders;

use App\Models\System\PlatformSetting;
use Illuminate\Database\Seeder;

class PlatformSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'platform_name',       'value' => 'EduVora',              'type' => 'text',     'group' => 'general',   'label' => 'Nama Platform',         'description' => 'Nama platform yang ditampilkan di sidebar, login, dan email.'],
            ['key' => 'platform_tagline',    'value' => 'School Management System', 'type' => 'text', 'group' => 'general',   'label' => 'Tagline',               'description' => 'Deskripsi singkat platform.'],
            ['key' => 'platform_logo',       'value' => null,                    'type' => 'image',    'group' => 'general',   'label' => 'Logo Platform',         'description' => 'Logo yang ditampilkan di sidebar dan login.'],
            ['key' => 'contact_email',       'value' => 'admin@eduvora.com',     'type' => 'email',    'group' => 'general',   'label' => 'Email Kontak',          'description' => 'Email utama untuk kontak platform.'],
            ['key' => 'contact_phone',       'value' => null,                    'type' => 'text',     'group' => 'general',   'label' => 'Telepon Kontak',        'description' => 'Nomor telepon kontak platform.'],

            ['key' => 'allow_registration',  'value' => 'true',                  'type' => 'boolean',  'group' => 'access',    'label' => 'Izinkan Registrasi',    'description' => 'Jika aktif, user baru bisa mendaftar sendiri.'],
            ['key' => 'maintenance_mode',    'value' => 'false',                 'type' => 'boolean',  'group' => 'access',    'label' => 'Mode Maintenance',      'description' => 'Jika aktif, hanya super-admin yang bisa mengakses.'],
            ['key' => 'maintenance_message', 'value' => 'Sistem sedang dalam pemeliharaan.', 'type' => 'textarea', 'group' => 'access', 'label' => 'Pesan Maintenance', 'description' => 'Pesan yang ditampilkan saat mode maintenance aktif.'],

            ['key' => 'default_school_type', 'value' => 'SMA/SMK',               'type' => 'select',   'group' => 'academic',  'label' => 'Tipe Sekolah Default',  'description' => 'Tipe sekolah default saat membuat sekolah baru.'],
            ['key' => 'academic_year_start', 'value' => '07',                    'type' => 'number',   'group' => 'academic',  'label' => 'Bulan Mulai Tahun Ajaran', 'description' => 'Bulan dimulainya tahun ajaran (1-12).'],
            ['key' => 'max_upload_size',     'value' => '5120',                  'type' => 'number',   'group' => 'academic',  'label' => 'Ukuran Upload Max (KB)', 'description' => 'Batas ukuran file upload dalam kilobyte.'],

            ['key' => 'email_notifications', 'value' => 'true',                 'type' => 'boolean',  'group' => 'notification', 'label' => 'Notifikasi Email',   'description' => 'Kirim notifikasi via email untuk event penting.'],
            ['key' => 'absence_notification', 'value' => 'true',                  'type' => 'boolean',  'group' => 'notification', 'label' => 'Notifikasi Ketidakhadiran', 'description' => 'Kirim notifikasi ke wali murid saat siswa tidak hadir.'],
        ];

        foreach ($settings as $s) {
            PlatformSetting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}
