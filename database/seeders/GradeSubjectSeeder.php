<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeSubjectSeeder extends Seeder
{
    public function run(): void
    {
        $schoolId = 1;

        /**
         * Ambil semua kelas berdasarkan school
         */
        $grades = DB::table('grades')
            ->where('school_id', $schoolId)
            ->get();

        /**
         * Ambil 5 mapel pertama
         */
        $subjects = DB::table('subjects')
            ->where('school_id', $schoolId)
            ->limit(5)
            ->get();

        /**
         * Ambil teacher berdasarkan relasi:
         * teachers.user_id
         * -> user_has_schools.user_id
         */
        $teachers = DB::table('teachers')
            ->join('user_has_schools', 'teachers.user_id', '=', 'user_has_schools.user_id')
            ->where('user_has_schools.school_id', $schoolId)
            ->select('teachers.teacher_id')
            ->pluck('teacher_id')
            ->toArray();

        $data = [];

        foreach ($grades as $grade) {

            foreach ($subjects as $index => $subject) {

                /**
                 * Teacher dibagi bergantian
                 */
                $teacherId = $teachers[$index % count($teachers)] ?? null;

                $data[] = [
                    'grade_id' => $grade->grade_id,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacherId,
                    'kkm' => 75,
                    'weight_harian' => 40,
                    'weight_uts' => 30,
                    'weight_uas' => 30,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('grade_subjects')->insert($data);
    }
}
