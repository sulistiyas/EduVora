<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $schoolId = 1;
        $semesterId = 1;

        /**
         * Hari sekolah
         */
        $days = [1, 2, 3, 4, 5];

        /**
         * Jam pelajaran
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
         * Ambil semua grade_subject per kelas
         */
        $gradeSubjects = DB::table('grade_subjects')
            ->join('grades', 'grade_subjects.grade_id', '=', 'grades.grade_id')
            ->where('grades.school_id', $schoolId)
            ->select(
                'grade_subjects.id as grade_subject_id',
                'grades.grade_id',
                'grades.room_id'
            )
            ->orderBy('grades.grade_id')
            ->get()
            ->groupBy('grade_id');

        $data = [];

        /**
         * Counter per room
         * supaya jadwal tidak bentrok
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

                $counter = $roomCounters[$roomId];

                /**
                 * Tentukan hari
                 */
                $day = $days[
                    floor($counter / count($timeSlots))
                    % count($days)
                ];

                /**
                 * Tentukan jam
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

        DB::table('schedules')->insert($data);
    }
}