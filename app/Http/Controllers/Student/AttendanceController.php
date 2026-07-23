<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $schoolId = getAuthSchoolId();

        $studentRecord = DB::table('students')
            ->where('user_id', $user->id)
            ->first();

        $studentId = $studentRecord?->id;
        $gradeId = $studentRecord?->grade_id;

        $activeSemester = DB::table('semesters')
            ->join('academic_years', 'semesters.academic_year_id', '=', 'academic_years.academic_year_id')
            ->where('semesters.status', 'active')
            ->where('academic_years.school_id', $schoolId)
            ->orderByDesc('semesters.created_at')
            ->select('semesters.*')
            ->first();

        $semesterId = $activeSemester?->semester_id;

        $attendanceHistory = [];
        $summary = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0, 'L' => 0, 'total' => 0];

        if ($studentId && $semesterId) {
            $rows = DB::table('attendance_details as adet')
                ->join('attendance_sessions as asess', 'asess.attendance_session_id', '=', 'adet.attendance_session_id')
                ->join('schedules as sc', 'sc.schedule_id', '=', 'asess.schedule_id')
                ->join('grade_subjects as gs', 'gs.id', '=', 'sc.grade_subject_id')
                ->join('subjects as sub', 'sub.id', '=', 'gs.subject_id')
                ->where('adet.student_id', $studentId)
                ->where('asess.semester_id', $semesterId)
                ->where('asess.school_id', $schoolId)
                ->orderByDesc('asess.attendance_date')
                ->orderBy('sc.start_time')
                ->select([
                    'asess.attendance_date',
                    'adet.status',
                    'adet.note',
                    'sub.subject_name as mapel',
                    'sc.start_time',
                    'sc.end_time',
                ])
                ->get();

            $statusLabel = ['H' => 'Hadir', 'I' => 'Izin', 'S' => 'Sakit', 'A' => 'Alpha', 'L' => 'Terlambat'];
            $statusBadge = ['H' => 'success', 'I' => 'primary', 'S' => 'warning', 'A' => 'danger', 'L' => 'secondary'];

            foreach ($rows as $row) {
                $summary[$row->status] = ($summary[$row->status] ?? 0) + 1;
                $summary['total']++;

                $attendanceHistory[] = [
                    'date'       => \Carbon\Carbon::parse($row->attendance_date)->translatedFormat('d M Y'),
                    'day'        => \Carbon\Carbon::parse($row->attendance_date)->translatedFormat('l'),
                    'mapel'      => $row->mapel,
                    'jam'        => substr($row->start_time, 0, 5) . ' – ' . substr($row->end_time, 0, 5),
                    'status'     => $statusLabel[$row->status] ?? $row->status,
                    'badge'      => $statusBadge[$row->status] ?? 'secondary',
                    'note'       => $row->note,
                ];
            }
        }

        $summary['persen_hadir'] = $summary['total'] > 0
            ? round($summary['H'] / $summary['total'] * 100, 1)
            : 0;

        $gradeName = null;
        if ($gradeId) {
            $gradeRecord = DB::table('grades')->where('grade_id', $gradeId)->first();
            $gradeName = $gradeRecord?->grade_name;
        }

        return view('pages.student.attendance.index', compact('attendanceHistory', 'summary', 'activeSemester', 'gradeName'));
    }
}
