<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $schoolId = 1;
        $academicYearId = 1;

        $grades = [
            [
                'grade_name' => 'X TKJ 1',
                'level' => 10,
                'room_id' => 1,
            ],
            [
                'grade_name' => 'X TKJ 2',
                'level' => 10,
                'room_id' => 2,
            ],
            [
                'grade_name' => 'XI TKJ 1',
                'level' => 11,
                'room_id' => 3,
            ],
            [
                'grade_name' => 'XII TKJ 1',
                'level' => 12,
                'room_id' => 4,
            ],
        ];

        foreach ($grades as $grade) {
            DB::table('grades')->insert([
                'school_id' => $schoolId,
                'academic_year_id' => $academicYearId,
                'room_id' => $grade['room_id'],
                'homeroom_teacher_id' => null,
                'grade_name' => $grade['grade_name'],
                'level' => $grade['level'],
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
