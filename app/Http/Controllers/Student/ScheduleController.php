<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $schoolId = getAuthSchoolId();

        $studentRecord = DB::table('students')
            ->where('user_id', $user->id)
            ->first();

        $gradeId = $studentRecord?->grade_id;

        $activeSemester = DB::table('semesters')
            ->join('academic_years', 'semesters.academic_year_id', '=', 'academic_years.academic_year_id')
            ->where('semesters.status', 'active')
            ->where('academic_years.school_id', $schoolId)
            ->orderByDesc('semesters.created_at')
            ->select('semesters.*')
            ->first();

        $semesterId = $activeSemester?->semester_id;

        $dayNames = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        $schedules = [];
        if ($gradeId && $semesterId) {
            $rows = DB::table('schedules as sc')
                ->join('grade_subjects as gs', 'gs.id', '=', 'sc.grade_subject_id')
                ->join('subjects as sub', 'sub.id', '=', 'gs.subject_id')
                ->join('rooms as r', 'r.room_id', '=', 'sc.room_id')
                ->join('teachers as t', 't.teacher_id', '=', 'gs.teacher_id')
                ->where('gs.grade_id', $gradeId)
                ->where('sc.semester_id', $semesterId)
                ->where('sc.status', 'active')
                ->orderBy('sc.day_of_week')
                ->orderBy('sc.start_time')
                ->select([
                    'sc.schedule_id',
                    'sc.day_of_week',
                    'sc.start_time',
                    'sc.end_time',
                    'sc.session_type',
                    'sub.subject_name as mapel',
                    'r.room_name as ruangan',
                    'r.code as ruangan_kode',
                    't.full_name as guru',
                ])
                ->get();

            $schedules = $rows->map(function ($s) use ($dayNames) {
                return [
                    'schedule_id' => $s->schedule_id,
                    'day'         => $dayNames[$s->day_of_week] ?? '-',
                    'day_num'     => $s->day_of_week,
                    'jam'         => substr($s->start_time, 0, 5) . ' – ' . substr($s->end_time, 0, 5),
                    'start'       => $s->start_time,
                    'mapel'       => $s->mapel,
                    'guru'        => $s->guru,
                    'ruangan'     => $s->ruangan_kode ?: $s->ruangan,
                    'tipe'        => $s->session_type,
                ];
            })->groupBy('day_num')->toArray();
        }

        $gradeName = null;
        if ($gradeId) {
            $gradeRecord = DB::table('grades')->where('grade_id', $gradeId)->first();
            $gradeName = $gradeRecord?->grade_name;
        }

        return view('pages.student.schedule.index', compact('schedules', 'dayNames', 'activeSemester', 'gradeName'));
    }
}
