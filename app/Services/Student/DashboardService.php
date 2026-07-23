<?php

namespace App\Services\Student;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(int $userId): array
    {
        $schoolId = DB::table('user_has_schools')
            ->where('user_id', $userId)
            ->value('school_id');

        $studentRecord = DB::table('students')
            ->where('user_id', $userId)
            ->first();

        $studentId = $studentRecord?->id;
        $gradeId = $studentRecord?->grade_id;

        $student = (object) [
            'name' => $studentRecord?->full_name ?? '-',
            'gender' => $studentRecord?->gender ?? 'male',
            'nis' => $studentRecord?->nis ?? '-',
            'photo' => null,
            'grade_name' => '—',
            'semester' => '—',
        ];

        if ($gradeId) {
            $gradeRecord = DB::table('grades')
                ->where('grade_id', $gradeId)
                ->first();
            $student->grade_name = $gradeRecord?->grade_name ?? '—';
        }

        $activeSemester = DB::table('semesters')
            ->join('academic_years', 'semesters.academic_year_id', '=', 'academic_years.academic_year_id')
            ->where('semesters.status', 'active')
            ->where('academic_years.school_id', $schoolId)
            ->orderByDesc('semesters.created_at')
            ->select('semesters.*')
            ->first();

        $semesterId = $activeSemester?->semester_id;

        if ($activeSemester) {
            $student->semester = $activeSemester->semester_name;
        }

        $dayOfWeekNum = Carbon::now()->dayOfWeekIso;

        $todaySchedulesRaw = DB::table('schedules as sc')
            ->join('grade_subjects as gs', 'gs.id', '=', 'sc.grade_subject_id')
            ->join('subjects as sub', 'sub.id', '=', 'gs.subject_id')
            ->join('rooms as r', 'r.room_id', '=', 'sc.room_id')
            ->join('teachers as t', 't.teacher_id', '=', 'gs.teacher_id')
            ->where('gs.grade_id', $gradeId)
            ->where('sc.semester_id', $semesterId)
            ->where('sc.status', 'active')
            ->where('sc.day_of_week', $dayOfWeekNum)
            ->orderBy('sc.start_time')
            ->select([
                'sc.schedule_id',
                'sc.start_time',
                'sc.end_time',
                'gs.id as grade_subject_id',
                'sub.id as subject_id',
                'sub.subject_name as mapel',
                'r.room_name as ruangan',
                'r.code as ruangan_kode',
                't.full_name as guru',
            ])
            ->get();

        $scheduleIds = $todaySchedulesRaw->pluck('schedule_id')->toArray();
        $today = Carbon::today();

        $attendanceMap = [];
        if ($scheduleIds && $studentId) {
            $rows = DB::table('attendance_sessions as asess')
                ->join('attendance_details as adet', 'adet.attendance_session_id', '=', 'asess.attendance_session_id')
                ->whereIn('asess.schedule_id', $scheduleIds)
                ->whereDate('asess.attendance_date', $today)
                ->where('adet.student_id', $studentId)
                ->select('asess.schedule_id', 'adet.status')
                ->get();

            foreach ($rows as $row) {
                $attendanceMap[$row->schedule_id] = $row->status;
            }
        }

        $statusLabel = ['H' => 'Hadir', 'I' => 'Izin', 'S' => 'Sakit', 'A' => 'Alpha', 'L' => 'Terlambat'];
        $statusBadge = ['H' => 'success', 'I' => 'primary', 'S' => 'warning', 'A' => 'danger', 'L' => 'secondary'];
        $nowTime = Carbon::now();

        $todaySchedules = $todaySchedulesRaw->map(function ($s) use ($attendanceMap, $statusLabel, $statusBadge, $nowTime) {
            $raw = $attendanceMap[$s->schedule_id] ?? null;

            if ($raw) {
                $badge = $statusBadge[$raw] ?? 'secondary';
                $label = $statusLabel[$raw] ?? $raw;
            } else {
                $start = Carbon::today()->setTimeFromTimeString($s->start_time);
                $end = Carbon::today()->setTimeFromTimeString($s->end_time);

                if ($nowTime->between($start, $end)) {
                    $badge = 'info';
                    $label = 'Berlangsung';
                } elseif ($nowTime->lt($start)) {
                    $badge = 'secondary';
                    $label = 'Akan Datang';
                } else {
                    $badge = 'light';
                    $label = 'Tidak ada data';
                }
            }

            return [
                'schedule_id' => $s->schedule_id,
                'grade_subject_id' => $s->grade_subject_id,
                'subject_id' => $s->subject_id,
                'jam' => substr($s->start_time, 0, 5).'–'.substr($s->end_time, 0, 5),
                'mapel' => $s->mapel,
                'guru' => $s->guru,
                'ruangan' => $s->ruangan_kode ?: $s->ruangan,
                'status_badge' => $badge,
                'status_label' => $label,
            ];
        })->toArray();

        $attendanceSummary = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0, 'L' => 0, 'total' => 0];

        if ($studentId && $semesterId) {
            $attRows = DB::table('attendance_details as adet')
                ->join('attendance_sessions as asess', 'asess.attendance_session_id', '=', 'adet.attendance_session_id')
                ->where('adet.student_id', $studentId)
                ->where('asess.semester_id', $semesterId)
                ->where('asess.school_id', $schoolId)
                ->selectRaw("
                    SUM(CASE WHEN adet.status = 'H' THEN 1 ELSE 0 END) as cnt_H,
                    SUM(CASE WHEN adet.status = 'I' THEN 1 ELSE 0 END) as cnt_I,
                    SUM(CASE WHEN adet.status = 'S' THEN 1 ELSE 0 END) as cnt_S,
                    SUM(CASE WHEN adet.status = 'A' THEN 1 ELSE 0 END) as cnt_A,
                    SUM(CASE WHEN adet.status = 'L' THEN 1 ELSE 0 END) as cnt_L,
                    COUNT(*) as total
                ")
                ->first();

            if ($attRows) {
                $attendanceSummary = [
                    'H' => (int) ($attRows->cnt_h ?? 0),
                    'I' => (int) ($attRows->cnt_i ?? 0),
                    'S' => (int) ($attRows->cnt_s ?? 0),
                    'A' => (int) ($attRows->cnt_a ?? 0),
                    'L' => (int) ($attRows->cnt_l ?? 0),
                    'total' => (int) ($attRows->total ?? 0),
                ];
            }
        }

        $attendanceSummary['persen_hadir'] = $attendanceSummary['total'] > 0
            ? round($attendanceSummary['H'] / $attendanceSummary['total'] * 100, 1)
            : 0;

        $nilaiPerMapel = [];
        $rataRataKeseluruhan = 0;

        if ($studentId && $gradeId && $semesterId) {
            $nilaiRaw = DB::table('score_details as sd')
                ->join('score_sessions as ss', 'ss.score_session_id', '=', 'sd.score_session_id')
                ->join('grade_subjects as gs', 'gs.id', '=', 'ss.grade_subject_id')
                ->join('subjects as sub', 'sub.id', '=', 'gs.subject_id')
                ->where('sd.student_id', $studentId)
                ->where('gs.grade_id', $gradeId)
                ->where('ss.semester_id', $semesterId)
                ->where('ss.is_published', true)
                ->groupBy('sub.id', 'sub.subject_name')
                ->selectRaw('
                    sub.id as subject_id,
                    sub.subject_name,
                    ROUND(AVG(sd.score), 1) as avg_score,
                    MAX(sd.score) as max_score,
                    MIN(sd.score) as min_score,
                    COUNT(sd.score_detail_id) as total_entries
                ')
                ->orderByDesc('avg_score')
                ->get();

            foreach ($nilaiRaw as $n) {
                $nilaiPerMapel[] = [
                    'subject_id' => $n->subject_id,
                    'mapel' => $n->subject_name,
                    'avg' => (float) $n->avg_score,
                    'max' => (float) $n->max_score,
                    'min' => (float) $n->min_score,
                    'total' => (int) $n->total_entries,
                    'persen' => min(100, round($n->avg_score, 0)),
                    'color' => $this->scoreColor((float) $n->avg_score),
                ];
            }

            $rataRataKeseluruhan = count($nilaiPerMapel) > 0
                ? round(collect($nilaiPerMapel)->avg('avg'), 1)
                : 0;
        }

        $rankingData = [];
        $rankingSiswaIni = null;

        if ($gradeId && $semesterId) {
            $rankingRaw = DB::table('score_details as sd')
                ->join('score_sessions as ss', 'ss.score_session_id', '=', 'sd.score_session_id')
                ->join('grade_subjects as gs', 'gs.id', '=', 'ss.grade_subject_id')
                ->join('students as stu', 'stu.id', '=', 'sd.student_id')
                ->where('gs.grade_id', $gradeId)
                ->where('ss.semester_id', $semesterId)
                ->where('ss.is_published', true)
                ->where('stu.status', 'active')
                ->groupBy('sd.student_id', 'stu.full_name')
                ->selectRaw('sd.student_id, stu.full_name, ROUND(AVG(sd.score), 1) as avg_score')
                ->orderByDesc('avg_score')
                ->get();

            $rank = 1;
            foreach ($rankingRaw as $r) {
                $isMe = ($r->student_id == $studentId);
                $entry = [
                    'rank' => $rank,
                    'student_id' => $r->student_id,
                    'nama' => $r->full_name,
                    'avg' => (float) $r->avg_score,
                    'is_me' => $isMe,
                ];

                if ($isMe) {
                    $rankingSiswaIni = $entry;
                }

                if ($rank <= 3 || $isMe) {
                    $rankingData[] = $entry;
                }

                $rank++;
            }

            $rankingData = collect($rankingData)->unique('student_id')->values()->toArray();
        }

        $totalSiswaKelas = DB::table('students')
            ->where('grade_id', $gradeId)
            ->where('status', 'active')
            ->count();

        $tugasPending = [];
        if ($studentId && $gradeId) {
            $tugasRaw = DB::table('assigments as a')
                ->join('subjects as sub', 'sub.id', '=', 'a.subject_id')
                ->leftJoin('assigment_submissions as asub', function ($join) use ($studentId) {
                    $join->on('asub.assigment_id', '=', 'a.id')
                        ->where('asub.student_id', '=', $studentId);
                })
                ->where('a.grade_id', $gradeId)
                ->whereNull('asub.id')
                ->where('a.due_date', '>=', now())
                ->orderBy('a.due_date')
                ->select(['a.id as assigment_id', 'a.title', 'a.due_date', 'sub.subject_name as mapel'])
                ->get();

            foreach ($tugasRaw as $t) {
                $dueDate = Carbon::parse($t->due_date);
                $daysLeft = (int) now()->diffInDays($dueDate, false);

                $tugasPending[] = [
                    'id' => $t->assigment_id,
                    'nama' => $t->title,
                    'mapel' => $t->mapel,
                    'due_date' => $dueDate->translatedFormat('d M Y'),
                    'days_left' => $daysLeft,
                    'urgency' => $daysLeft <= 1 ? 'danger' : ($daysLeft <= 3 ? 'warning' : 'secondary'),
                ];
            }
        }

        $tugasSubmitted = [];
        if ($studentId && $gradeId) {
            $submittedRaw = DB::table('assigments as a')
                ->join('subjects as sub', 'sub.id', '=', 'a.subject_id')
                ->join('assigment_submissions as asub', 'asub.assigment_id', '=', 'a.id')
                ->where('a.grade_id', $gradeId)
                ->where('asub.student_id', $studentId)
                ->orderByDesc('asub.submitted_at')
                ->take(5)
                ->select(['a.id as assigment_id', 'a.title', 'sub.subject_name as mapel', 'asub.submitted_at', 'asub.feedback'])
                ->get();

            foreach ($submittedRaw as $t) {
                $tugasSubmitted[] = [
                    'id' => $t->assigment_id,
                    'nama' => $t->title,
                    'mapel' => $t->mapel,
                    'submitted_at' => Carbon::parse($t->submitted_at)->translatedFormat('d M Y, H:i'),
                    'feedback' => $t->feedback,
                    'graded' => $t->feedback !== null,
                ];
            }
        }

        $statistics = [
            'rata_rata_nilai' => $rataRataKeseluruhan,
            'persen_kehadiran' => $attendanceSummary['persen_hadir'],
            'tugas_pending' => count($tugasPending),
            'ranking' => $rankingSiswaIni['rank'] ?? '-',
            'total_siswa_kelas' => $totalSiswaKelas,
            'jadwal_hari_ini' => count($todaySchedules),
        ];

        $announcements = [];
        $academicEvents = [];

        return compact(
            'student',
            'statistics',
            'todaySchedules',
            'attendanceSummary',
            'nilaiPerMapel',
            'rankingData',
            'rankingSiswaIni',
            'totalSiswaKelas',
            'tugasPending',
            'tugasSubmitted',
            'announcements',
            'academicEvents'
        );
    }

    private function scoreColor(float $score): string
    {
        if ($score >= 85) {
            return '#10B981';
        }
        if ($score >= 75) {
            return '#3B82F6';
        }
        if ($score >= 65) {
            return '#F59E0B';
        }

        return '#EF4444';
    }
}
