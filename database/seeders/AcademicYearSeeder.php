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

        /**
         * Tahun sekarang
         */
        $currentYear = now()->year;

        /**
         * Contoh:
         * Sekarang 2026
         *
         * Generate:
         * 2025/2026
         * 2026/2027
         * 2027/2028
         * 2028/2029
         * 2029/2030
         */
        $startAcademicYear = $currentYear - 1;

        /**
         * Tanggal hari ini
         */
        $today = now();

        foreach ($schools as $school) {

            for ($i = 0; $i < 5; $i++) {

                $yearStart = $startAcademicYear + $i;
                $yearEnd   = $yearStart + 1;

                $academicYearName = "{$yearStart}/{$yearEnd}";

                /**
                 * Academic Year Dates
                 */
                $academicStartDate = Carbon::parse("{$yearStart}-07-01");
                $academicEndDate   = Carbon::parse("{$yearEnd}-06-30");

                /**
                 * Academic Year Active Status
                 */
                $academicStatus = $today->between(
                    $academicStartDate,
                    $academicEndDate
                )
                    ? 'active'
                    : 'inactive';

                /**
                 * Insert Academic Year
                 */
                $academicYearId = DB::table('academic_years')->insertGetId([
                    'school_id'          => $school->school_id,
                    'academic_year_name' => $academicYearName,
                    'start_date'         => $academicStartDate->format('Y-m-d'),
                    'end_date'           => $academicEndDate->format('Y-m-d'),
                    'status'             => $academicStatus,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ], 'academic_year_id');

                /**
                 * ─────────────────────────────────────────────
                 * SEMESTER GANJIL
                 * Juli - Desember
                 * ─────────────────────────────────────────────
                 */
                $ganjilStart = Carbon::parse("{$yearStart}-07-01");
                $ganjilEnd   = Carbon::parse("{$yearStart}-12-31");

                $ganjilStatus = $today->between(
                    $ganjilStart,
                    $ganjilEnd
                )
                    ? 'active'
                    : 'inactive';

                DB::table('semesters')->insert([
                    'semester_name'      => "Semester Ganjil {$academicYearName}",
                    'academic_year_id'   => $academicYearId,

                    'start_date'         => $ganjilStart->format('Y-m-d'),
                    'end_date'           => $ganjilEnd->format('Y-m-d'),

                    'midterm_start_date' => "{$yearStart}-09-15",
                    'midterm_end_date'   => "{$yearStart}-09-19",

                    'final_start_date'   => "{$yearStart}-12-08",
                    'final_end_date'     => "{$yearStart}-12-12",

                    'status'             => $ganjilStatus,

                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);

                /**
                 * ─────────────────────────────────────────────
                 * SEMESTER GENAP
                 * Januari - Juni
                 * ─────────────────────────────────────────────
                 */
                $genapStart = Carbon::parse("{$yearEnd}-01-01");
                $genapEnd   = Carbon::parse("{$yearEnd}-06-30");

                $genapStatus = $today->between(
                    $genapStart,
                    $genapEnd
                )
                    ? 'active'
                    : 'inactive';

                DB::table('semesters')->insert([
                    'semester_name'      => "Semester Genap {$academicYearName}",
                    'academic_year_id'   => $academicYearId,

                    'start_date'         => $genapStart->format('Y-m-d'),
                    'end_date'           => $genapEnd->format('Y-m-d'),

                    'midterm_start_date' => "{$yearEnd}-03-09",
                    'midterm_end_date'   => "{$yearEnd}-03-13",

                    'final_start_date'   => "{$yearEnd}-06-08",
                    'final_end_date'     => "{$yearEnd}-06-12",

                    'status'             => $genapStatus,

                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }
        }
    }
}