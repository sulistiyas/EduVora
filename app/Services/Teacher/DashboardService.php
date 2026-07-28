<?php

namespace App\Services\Teacher;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(int $userId): array
    {
        $schoolId = DB::table('user_has_schools')
            ->where('user_id', $userId)
            ->value('school_id');

        $teacherRecord = DB::table('teachers')
            ->where('user_id', $userId)
            ->first();

        $teacherId = $teacherRecord?->teacher_id;

        $activeSemester = DB::table('semesters')
            ->join('academic_years', 'semesters.academic_year_id', '=', 'academic_years.academic_year_id')
            ->where('semesters.status', 'active')
            ->where('academic_years.school_id', $schoolId)
            ->orderByDesc('semesters.created_at')
            ->select('semesters.*')
            ->first();

        $semesterId = $activeSemester?->semester_id;

        $teacher = (object) [
            'name' => $teacherRecord?->full_name ?? '-',
            'gender' => $teacherRecord?->gender ?? 'male',
            'nip' => $teacherRecord?->nip ?? '-',
            'photo' => null,
            'semester' => $activeSemester?->semester_name ?? '-',
            'mapel' => [],
        ];

        $mapelList = DB::table('grade_subjects as gs')
            ->join('subjects as s', 's.id', '=', 'gs.subject_id')
            ->join('grades as g', 'g.grade_id', '=', 'gs.grade_id')
            ->where('gs.teacher_id', $teacherId)
            ->where('g.school_id', $schoolId)
            ->distinct()
            ->pluck('s.subject_name')
            ->toArray();

        $teacher->mapel = $mapelList ?: ['—'];

        $gradeIds = DB::table('grade_subjects as gs')
            ->join('grades as g', 'g.grade_id', '=', 'gs.grade_id')
            ->where('gs.teacher_id', $teacherId)
            ->where('g.school_id', $schoolId)
            ->distinct()
            ->pluck('gs.grade_id')
            ->toArray();

        $today = Carbon::now();
        $dayOfWeekNum = $today->dayOfWeekIso;

        $todaySchedulesRaw = DB::table('schedules as sc')
            ->join('grade_subjects as gs', 'gs.id', '=', 'sc.grade_subject_id')
            ->join('subjects as sub', 'sub.id', '=', 'gs.subject_id')
            ->join('grades as g', 'g.grade_id', '=', 'gs.grade_id')
            ->join('rooms as r', 'r.room_id', '=', 'sc.room_id')
            ->where('gs.teacher_id', $teacherId)
            ->where('g.school_id', $schoolId)
            ->where('sc.semester_id', $semesterId)
            ->where('sc.status', 'active')
            ->where('sc.day_of_week', $dayOfWeekNum)
            ->orderBy('sc.start_time')
            ->select([
                'sc.schedule_id',
                'sc.start_time',
                'sc.end_time',
                'sc.session_type',
                'g.grade_name as kelas',
                'sub.subject_name as mapel',
                'r.room_name as ruangan',
                'r.code as ruangan_kode',
            ])
            ->get();

        $todaySchedules = $todaySchedulesRaw->map(function ($s) {
            return [
                'schedule_id' => $s->schedule_id,
                'jam' => substr($s->start_time, 0, 5).'–'.substr($s->end_time, 0, 5),
                'kelas' => $s->kelas,
                'mapel' => $s->mapel,
                'ruangan' => $s->ruangan_kode ?: $s->ruangan,
                'status_absensi' => 'belum',
                'status_jurnal' => 'belum',
            ];
        })->toArray();

        $totalKelas = DB::table('schedules as sc')
            ->join('grade_subjects as gs', 'gs.id', '=', 'sc.grade_subject_id')
            ->join('grades as g', 'g.grade_id', '=', 'gs.grade_id')
            ->where('gs.teacher_id', $teacherId)
            ->where('g.school_id', $schoolId)
            ->where('sc.semester_id', $semesterId)
            ->where('sc.status', 'active')
            ->distinct('gs.grade_id')
            ->count('gs.grade_id');

        $totalSiswa = DB::table('students')
            ->whereIn('grade_id', $gradeIds)
            ->where('status', 'active')
            ->count();

        $jadwalHariIni = count($todaySchedules);

        $pendingSubmissionsCount = DB::table('assigment_submissions as sub')
            ->join('assigments as a', 'a.id', '=', 'sub.assigment_id')
            ->where('a.teacher_id', $teacherId)
            ->where('sub.status', 'submitted')
            ->count();

        $statistics = [
            'total_kelas' => $totalKelas,
            'total_siswa' => $totalSiswa,
            'jadwal_hari_ini' => $jadwalHariIni,
            'tugas_belum_dinilai' => $pendingSubmissionsCount,
            'absensi_belum_diisi' => 0,
        ];

        $pendingAssignments = [];
        $attendanceSummary = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpha' => 0,
            'total' => $totalSiswa ?: 1,
        ];
        $announcements = [];
        $academicEvents = [];
        $recentActivities = [];

        return compact(
            'teacher',
            'statistics',
            'todaySchedules',
            'pendingAssignments',
            'attendanceSummary',
            'announcements',
            'academicEvents',
            'recentActivities'
        );
    }
}
