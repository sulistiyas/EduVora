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
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->count();

        $totalGuru = DB::table('teachers')
            ->where('school_id', $schoolId)
            ->where('status', 'active')
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
            ->where('status', 'active')
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
                ->join('grade_subjects as gs', 'gs.id', '=', 'a.grade_subject_id')
                ->join('grades as g', 'g.grade_id', '=', 'gs.grade_id')
                ->where('g.school_id', $schoolId)
                ->where('a.due_date', '>=', now())
                ->count();
        }

        $recentLogs = DB::table('audit_logs as al')
            ->leftJoin('users as u', 'u.id', '=', 'al.user_id')
            ->where('al.school_id', $schoolId)
            ->orderByDesc('al.created_at')
            ->take(10)
            ->select([
                'al.audit_log_id',
                'al.description',
                'al.module',
                'al.status',
                'al.created_at',
                'u.name as user_name',
                'u.email as user_email',
            ])
            ->get();

        $statusBadge = ['success' => 'success', 'warning' => 'warning', 'error' => 'danger', 'info' => 'info'];
        $statusLabel = ['success' => 'Berhasil', 'warning' => 'Peringatan', 'error' => 'Gagal', 'info' => 'Info'];

        $recentLogs = $recentLogs->map(function ($log) use ($statusBadge, $statusLabel) {
            $log->status_badge = $statusBadge[$log->status] ?? 'secondary';
            $log->status_label = $statusLabel[$log->status] ?? ucfirst($log->status ?? '-');

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
