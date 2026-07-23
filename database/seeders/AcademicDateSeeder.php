<?php

namespace Database\Seeders;

use App\Models\Academic\AcademicDate;
use Illuminate\Database\Seeder;

class AcademicDateSeeder extends Seeder
{
    public function run(): void
    {
        $schoolId = 1;

        $dates = [
            [
                'title' => 'Libur Nasional - Hari Kemerdekaan',
                'description' => 'Hari Kemerdekaan Republik Indonesia ke-81',
                'start_date' => '2026-08-17',
                'end_date' => null,
                'type' => 'holiday',
                'status' => 'active',
            ],
            [
                'title' => 'Libur Semester Ganjil',
                'description' => 'Libur akhir semester ganjil',
                'start_date' => '2026-12-19',
                'end_date' => '2027-01-02',
                'type' => 'holiday',
                'status' => 'active',
            ],
            [
                'title' => 'Ujian Tengah Semester (UTS)',
                'description' => 'Pelaksanaan UTS semester ganjil',
                'start_date' => '2026-10-12',
                'end_date' => '2026-10-16',
                'type' => 'exam',
                'status' => 'active',
            ],
            [
                'title' => 'Ujian Akhir Semester (UAS)',
                'description' => 'Pelaksanaan UAS semester ganjil',
                'start_date' => '2026-12-08',
                'end_date' => '2026-12-18',
                'type' => 'exam',
                'status' => 'active',
            ],
            [
                'title' => 'Batas Input Nilai UTS',
                'description' => 'Deadline guru menginput nilai UTS',
                'start_date' => '2026-10-23',
                'end_date' => null,
                'type' => 'deadline',
                'status' => 'active',
            ],
            [
                'title' => 'Hari Guru Nasional',
                'description' => 'Peringatan Hari Guru Nasional',
                'start_date' => '2026-11-25',
                'end_date' => null,
                'type' => 'event',
                'status' => 'active',
            ],
        ];

        foreach ($dates as $date) {
            AcademicDate::create(array_merge($date, ['school_id' => $schoolId]));
        }
    }
}
