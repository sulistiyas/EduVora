<?php

namespace App\Services\SchoolAdmin;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(int $schoolId, int $userId): array
    {
        $school = DB::table('school_profiles')
            ->where('school_id', $schoolId)
            ->first();

        $totalSiswa = DB::table('students')
            ->join('user_has_schools', 'students.user_id', '=', 'user_has_schools.user_id')
            ->where('user_has_schools.school_id', $schoolId)
            ->where('students.status', 'active')
            ->count();

        $totalGuru = DB::table('teachers')
            ->join('user_has_schools', 'teachers.user_id', '=', 'user_has_schools.user_id')
            ->where('user_has_schools.school_id', $schoolId)
            ->where('teachers.status', 'active')
            ->count();

        $totalKelas = DB::table('grades')
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->count();

        $totalMapel = DB::table('grade_subjects as gs')
            ->join('grades as g', 'g.grade_id', '=', 'gs.grade_id')
            ->where('g.school_id', $schoolId)
            ->distinct('gs.subject_id')
            ->count('gs.subject_id');

        $totalRuangan = DB::table('rooms')
            ->where('school_id', $schoolId)
            ->where('status', 'available')
            ->count();

        $activeSemester = DB::table('semesters')
            ->join('academic_years', 'semesters.academic_year_id', '=', 'academic_years.academic_year_id')
            ->where('semesters.status', 'active')
            ->where('academic_years.school_id', $schoolId)
            ->orderByDesc('semesters.created_at')
            ->select('semesters.*')
            ->first();

        $semesterId = $activeSemester?->semester_id;

        $todayHadir = 0;
        $todayAlpha = 0;
        $persenHadir = 0;

        if ($semesterId) {
            $today = Carbon::today();

            $attRows = DB::table('attendance_sessions as asess')
                ->join('attendance_details as adet', 'adet.attendance_session_id', '=', 'asess.attendance_session_id')
                ->where('asess.school_id', $schoolId)
                ->where('asess.semester_id', $semesterId)
                ->whereDate('asess.attendance_date', $today)
                ->selectRaw("
                    SUM(CASE WHEN adet.status = 'H' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN adet.status IN ('A','I','S','L') THEN 1 ELSE 0 END) as alpha,
                    COUNT(*) as total
                ")
                ->first();

            if ($attRows && $attRows->total > 0) {
                $todayHadir = (int) $attRows->hadir;
                $todayAlpha = (int) $attRows->alpha;
                $persenHadir = round($attRows->hadir / $attRows->total * 100, 1);
            }
        }

        $totalTugas = 0;
        if ($semesterId) {
            $totalTugas = DB::table('assigments as a')
                ->join('grades as g', 'g.grade_id', '=', 'a.grade_id')
                ->where('g.school_id', $schoolId)
                ->where('a.due_date', '>=', now())
                ->count();
        }

        $recentLogs = DB::table('audit_logs as al')
            ->join('user_has_schools as me', 'me.user_id', '=', 'al.user_id')
            ->leftJoin('users as u', 'u.id', '=', 'al.user_id')
            ->where('me.school_id', $schoolId)
            ->orderByDesc('al.created_at')
            ->take(10)
            ->select([
                'al.id as audit_log_id',
                'al.action as description',
                DB::raw("COALESCE(al.table_name, 'Sistem') as module"),
                'al.created_at',
                'u.name as user_name',
                'u.email as user_email',
            ])
            ->get()
            ->map(function ($log) {
                $log->status_badge = 'success';
                $log->status_label = 'Berhasil';

                return $log;
            });

        return compact(
            'school',
            'totalSiswa',
            'totalGuru',
            'totalKelas',
            'totalMapel',
            'totalRuangan',
            'activeSemester',
            'todayHadir',
            'todayAlpha',
            'persenHadir',
            'totalTugas',
            'recentLogs',
        );
    }
}
