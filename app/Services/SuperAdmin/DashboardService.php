<?php

namespace App\Services\SuperAdmin;

use App\Models\Activity\AttendanceDetail;
use App\Models\Activity\AttendanceSession;
use App\Models\Core\AuditLog;
use App\Models\Core\SchoolProfiles;
use App\Models\Core\User;
use App\Models\Finance\StudentInvoice;
use App\Models\Student\Student;
use App\Models\Teacher\Teacher;

class DashboardService
{
    public function getStats(): array
    {
        $totalSiswa = Student::count();
        $totalGuru = Teacher::count();
        $totalSekolah = SchoolProfiles::count();
        $totalUsers = User::count();
        $totalSuperAdmin = User::whereHas('roles', fn ($q) => $q->where('role_name', 'super-admin'))->count();
        $totalAdminSekolah = User::whereHas('roles', fn ($q) => $q->where('role_name', 'school-admin'))->count();

        $userStatusCounts = [
            'active' => User::where('status', 'active')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
        ];

        $schoolTypes = [
            'SMA/SMK' => SchoolProfiles::where('school_type', 'Senior High')->count(),
            'SMP' => SchoolProfiles::where('school_type', 'Junior High')->count(),
            'SD' => SchoolProfiles::where('school_type', 'Elementary')->count(),
        ];

        $todaySessionIds = AttendanceSession::where('attendance_date', now()->toDateString())
            ->pluck('attendance_session_id');

        $todayHadir = AttendanceDetail::whereIn('attendance_session_id', $todaySessionIds)
            ->where('status', 'H')
            ->count();

        $todayTotal = AttendanceDetail::whereIn('attendance_session_id', $todaySessionIds)
            ->count();

        $todayAlpha = AttendanceDetail::whereIn('attendance_session_id', $todaySessionIds)
            ->whereIn('status', ['A', 'I', 'S'])
            ->count();

        $persenHadir = $todayTotal > 0 ? round(($todayHadir / $todayTotal) * 100) : 0;

        $totalTagihanBelumLunas = StudentInvoice::whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->sum('amount');

        $jumlahTunggakan = StudentInvoice::whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->distinct('student_id')
            ->count('student_id');

        $recentLogs = AuditLog::with('user:id,name,email')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($log) => (object) [
                'user' => $log->user,
                'description' => $log->action,
                'module' => $log->table_name ?? 'Sistem',
                'status_badge' => 'success',
                'status_label' => 'Berhasil',
                'created_at' => $log->created_at,
            ]);

        return compact(
            'totalSiswa',
            'totalGuru',
            'totalSekolah',
            'totalUsers',
            'totalSuperAdmin',
            'totalAdminSekolah',
            'userStatusCounts',
            'schoolTypes',
            'todayHadir',
            'todayTotal',
            'todayAlpha',
            'persenHadir',
            'totalTagihanBelumLunas',
            'jumlahTunggakan',
            'recentLogs',
        );
    }
}
