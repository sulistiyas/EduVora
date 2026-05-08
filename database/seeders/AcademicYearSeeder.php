<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schools = DB::table('school_profiles')->get();

        // Tahun sekarang
        $currentYear = now()->year;

        /**
         * Contoh:
         * Sekarang 2026
         * Maka:
         * 2025/2026  <- 1 tahun kebelakang
         * 2026/2027
         * 2027/2028
         * 2028/2029
         * 2029/2030 <- 4 tahun kedepan
         */
        $startAcademicYear = $currentYear - 1;

        foreach ($schools as $school) {

            for ($i = 0; $i < 5; $i++) {

                $yearStart = $startAcademicYear + $i;
                $yearEnd   = $yearStart + 1;

                $academicYearName = "{$yearStart}/{$yearEnd}";

                // Active hanya tahun ajaran sekarang
                $academicStatus =
                    ($yearStart === $currentYear)
                        ? 'active'
                        : 'inactive';

                // Insert Academic Year
                $academicYearId = DB::table('academic_years')->insertGetId([
                    'school_id'          => $school->school_id,
                    'academic_year_name' => $academicYearName,
                    'start_date'         => "{$yearStart}-07-01",
                    'end_date'           => "{$yearEnd}-06-30",
                    'status'             => $academicStatus,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ], 'academic_year_id');

                /**
                 * SEMESTER GANJIL
                 */
                DB::table('semesters')->insert([
                    'semester_name'      => "Semester Ganjil {$academicYearName}",
                    'academic_year_id'   => $academicYearId,
                    'start_date'         => "{$yearStart}-07-01",
                    'end_date'           => "{$yearStart}-12-31",

                    'midterm_start_date' => "{$yearStart}-09-15",
                    'midterm_end_date'   => "{$yearStart}-09-19",

                    'final_start_date'   => "{$yearStart}-12-08",
                    'final_end_date'     => "{$yearStart}-12-12",

                    'status'             => $academicStatus,

                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);

                /**
                 * SEMESTER GENAP
                 */
                DB::table('semesters')->insert([
                    'semester_name'      => "Semester Genap {$academicYearName}",
                    'academic_year_id'   => $academicYearId,
                    'start_date'         => "{$yearEnd}-01-01",
                    'end_date'           => "{$yearEnd}-06-30",

                    'midterm_start_date' => "{$yearEnd}-03-09",
                    'midterm_end_date'   => "{$yearEnd}-03-13",

                    'final_start_date'   => "{$yearEnd}-06-08",
                    'final_end_date'     => "{$yearEnd}-06-12",

                    'status'             => 'inactive',

                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }
        }
    }
}