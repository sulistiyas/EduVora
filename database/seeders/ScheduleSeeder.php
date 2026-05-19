<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $schoolId = 1;

        /**
         * ─────────────────────────────────────────────
         * Academic Year aktif
         * ─────────────────────────────────────────────
         */
        $activeAcademicYear = DB::table('academic_years')
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->first();

        if (! $activeAcademicYear) {
            $this->command->error('Tidak ada academic year aktif.');
            return;
        }

        /**
         * ─────────────────────────────────────────────
         * Semester aktif
         * ─────────────────────────────────────────────
         */
        $activeSemester = DB::table('semesters as s')
            ->join('academic_years as ay', 'ay.academic_year_id', '=', 's.academic_year_id')
            ->where('ay.school_id', $schoolId)
            ->where('ay.status', 'active')
            ->where('s.status', 'active')
            ->select('s.*')
            ->first();

        if (! $activeSemester) {
            $this->command->error('Tidak ada semester aktif.');
            return;
        }

        $semesterId = $activeSemester->semester_id;

        $this->command->info(
            "Menggunakan Semester Aktif: {$activeSemester->semester_name}"
        );

        /**
         * ─────────────────────────────────────────────
         * Hari sekolah
         * 1 = Senin
         * 2 = Selasa
         * 3 = Rabu
         * 4 = Kamis
         * 5 = Jumat
         * ─────────────────────────────────────────────
         */
        $days = [1, 2, 3, 4, 5];

        /**
         * ─────────────────────────────────────────────
         * Slot jam pelajaran
         * ─────────────────────────────────────────────
         */
        $timeSlots = [
            [
                'start' => '07:00:00',
                'end'   => '08:30:00',
            ],
            [
                'start' => '08:30:00',
                'end'   => '10:00:00',
            ],
            [
                'start' => '10:15:00',
                'end'   => '11:45:00',
            ],
            [
                'start' => '13:00:00',
                'end'   => '14:30:00',
            ],
            [
                'start' => '14:30:00',
                'end'   => '16:00:00',
            ],
        ];

        /**
         * ─────────────────────────────────────────────
         * Ambil semua grade_subject
         * ─────────────────────────────────────────────
         */
        $gradeSubjects = DB::table('grade_subjects')
            ->join('grades', 'grade_subjects.grade_id', '=', 'grades.grade_id')
            ->join('subjects', 'grade_subjects.subject_id', '=', 'subjects.id')
            ->where('grades.school_id', $schoolId)
            ->select(
                'grade_subjects.id as grade_subject_id',
                'grades.grade_id',
                'grades.room_id',
                'subjects.subject_name',
                'subjects.hours_per_week'
            )
            ->orderBy('grades.grade_id')
            ->get()
            ->groupBy('grade_id');

        $data = [];

        /**
         * Counter per room
         */
        $roomCounters = [];

        foreach ($gradeSubjects as $gradeId => $subjects) {

            foreach ($subjects as $subject) {

                $roomId = $subject->room_id;

                /**
                 * Init counter room
                 */
                if (! isset($roomCounters[$roomId])) {
                    $roomCounters[$roomId] = 0;
                }

                /**
                 * ─────────────────────────────────────
                 * Jumlah sesi per minggu
                 * contoh:
                 * hours_per_week = 4
                 * maka jadi 2 sesi
                 * ─────────────────────────────────────
                 */
                $sessionsPerWeek = max(
                    1,
                    ceil($subject->hours_per_week / 2)
                );

                /**
                 * Generate beberapa sesi
                 */
                for ($i = 0; $i < $sessionsPerWeek; $i++) {

                    $counter = $roomCounters[$roomId];

                    /**
                     * Tentukan hari
                     */
                    $day = $days[
                        floor($counter / count($timeSlots))
                        % count($days)
                    ];

                    /**
                     * Tentukan slot jam
                     */
                    $time = $timeSlots[
                        $counter % count($timeSlots)
                    ];

                    $data[] = [
                        'school_id'        => $schoolId,
                        'grade_subject_id' => $subject->grade_subject_id,
                        'room_id'          => $roomId,
                        'semester_id'      => $semesterId,
                        'day_of_week'      => $day,
                        'start_time'       => $time['start'],
                        'end_time'         => $time['end'],
                        'session_type'     => 'regular',
                        'status'           => 'active',
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];

                    /**
                     * Increment slot room
                     */
                    $roomCounters[$roomId]++;
                }
            }
        }

        /**
         * ─────────────────────────────────────────────
         * Hapus jadwal lama semester aktif
         * ─────────────────────────────────────────────
         */
        DB::table('schedules')
            ->where('school_id', $schoolId)
            ->where('semester_id', $semesterId)
            ->delete();

        /**
         * Insert jadwal baru
         */
        DB::table('schedules')->insert($data);

        $this->command->info(
            count($data) . ' jadwal berhasil dibuat.'
        );
    }
}