<?php

namespace App\Repositories\Parent;

use App\Concerns\HasSchoolScope;
use App\Models\Student\Student;
use App\Models\Student\StudentParent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ParentRepository
{
    use HasSchoolScope;

    /**
     * Get parent's linked student.
     */
    public function getChildDataByUserId(int $userId): ?object
    {
        $schoolId = DB::table('user_has_schools')
            ->where('user_id', $userId)
            ->value('school_id');

        $parentRecord = StudentParent::where('user_id', $userId)->first();

        if (! $parentRecord) {
            $userEmail = DB::table('users')->where('id', $userId)->value('email');
            if ($userEmail) {
                $parentRecord = StudentParent::where('email', $userEmail)->first();
            }
        }

        $studentId = $parentRecord?->student_id;

        if ($studentId) {
            $student = Student::with(['grade'])->find($studentId);
        } else {
            $student = Student::with(['grade'])
                ->where('school_id', $schoolId)
                ->first();
        }

        if (! $student) {
            return null;
        }

        $activeSemester = DB::table('semesters')
            ->join('academic_years', 'semesters.academic_year_id', '=', 'academic_years.academic_year_id')
            ->where('semesters.status', 'active')
            ->where('academic_years.school_id', $student->school_id)
            ->orderByDesc('semesters.created_at')
            ->select('semesters.*', 'academic_years.academic_year_name as year_name')
            ->first();

        $homeroomTeacher = null;
        if ($student->grade_id) {
            $homeroomTeacher = DB::table('grades as g')
                ->leftJoin('teachers as t', 't.teacher_id', '=', 'g.teacher_id')
                ->where('g.grade_id', $student->grade_id)
                ->select('t.full_name', 't.phone_number', 't.email', 'g.grade_name')
                ->first();
        }

        return (object) [
            'student' => $student,
            'student_id' => $student->id,
            'school_id' => $student->school_id,
            'grade_id' => $student->grade_id,
            'grade_name' => $student->grade?->grade_name ?? ($homeroomTeacher?->grade_name ?? '—'),
            'homeroom_teacher' => $homeroomTeacher?->full_name ?? 'Belum ditentukan',
            'homeroom_phone' => $homeroomTeacher?->phone_number ?? '-',
            'active_semester' => $activeSemester,
            'parent_name' => $parentRecord?->parent_name ?? 'Orang Tua',
            'relationship' => $parentRecord?->relationship ?? 'Orang Tua',
        ];
    }

    /**
     * Get attendance summary stats.
     */
    public function getAttendanceSummary(int $studentId, ?int $semesterId): array
    {
        $summary = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0, 'L' => 0, 'total' => 0, 'persen_hadir' => 0];
        if (! $semesterId) {
            return $summary;
        }

        $attRows = DB::table('attendance_details as adet')
            ->join('attendance_sessions as asess', 'asess.attendance_session_id', '=', 'adet.attendance_session_id')
            ->where('adet.student_id', $studentId)
            ->where('asess.semester_id', $semesterId)
            ->selectRaw("
                SUM(CASE WHEN adet.status = 'H' THEN 1 ELSE 0 END) as cnt_H,
                SUM(CASE WHEN adet.status = 'I' THEN 1 ELSE 0 END) as cnt_I,
                SUM(CASE WHEN adet.status = 'S' THEN 1 ELSE 0 END) as cnt_S,
                SUM(CASE WHEN adet.status = 'A' THEN 1 ELSE 0 END) as cnt_A,
                SUM(CASE WHEN adet.status = 'L' THEN 1 ELSE 0 END) as cnt_L,
                COUNT(*) as total
            ")
            ->first();

        if ($attRows && $attRows->total > 0) {
            $h = (int) ($attRows->cnt_h ?? 0);
            $tot = (int) $attRows->total;
            $summary = [
                'H' => $h,
                'I' => (int) ($attRows->cnt_i ?? 0),
                'S' => (int) ($attRows->cnt_s ?? 0),
                'A' => (int) ($attRows->cnt_a ?? 0),
                'L' => (int) ($attRows->cnt_l ?? 0),
                'total' => $tot,
                'persen_hadir' => round(($h / $tot) * 100, 1),
            ];
        }

        return $summary;
    }

    /**
     * Get today's schedule for child's grade.
     */
    public function getTodaySchedules(?int $gradeId, ?int $semesterId): array
    {
        if (! $gradeId || ! $semesterId) {
            return [];
        }

        $dayOfWeekNum = Carbon::now()->dayOfWeekIso;

        $rows = DB::table('schedules as sc')
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
                'sub.subject_name as mapel',
                'r.room_name as ruangan',
                't.full_name as guru',
            ])
            ->get();

        return $rows->map(function ($s) {
            return [
                'jam' => substr($s->start_time, 0, 5).'–'.substr($s->end_time, 0, 5),
                'mapel' => $s->mapel,
                'guru' => $s->guru,
                'ruangan' => $s->ruangan,
            ];
        })->toArray();
    }

    /**
     * Get published scores per subject for child.
     */
    public function getNilaiPerMapel(int $studentId, ?int $gradeId, ?int $semesterId): array
    {
        if (! $gradeId || ! $semesterId) {
            return [];
        }

        $rows = DB::table('score_details as sd')
            ->join('score_sessions as ss', 'ss.score_session_id', '=', 'sd.score_session_id')
            ->join('grade_subjects as gs', 'gs.id', '=', 'ss.grade_subject_id')
            ->join('subjects as sub', 'sub.id', '=', 'gs.subject_id')
            ->where('sd.student_id', $studentId)
            ->where('gs.grade_id', $gradeId)
            ->where('ss.semester_id', $semesterId)
            ->where('ss.is_published', true)
            ->groupBy('sub.id', 'sub.subject_name')
            ->selectRaw('sub.subject_name, ROUND(AVG(sd.score), 1) as avg_score')
            ->get();

        return $rows->map(function ($n) {
            return [
                'mapel' => $n->subject_name,
                'avg' => (float) $n->avg_score,
            ];
        })->toArray();
    }

    /**
     * Get full attendance logs.
     */
    public function getAttendanceLogs(int $studentId, ?int $semesterId, array $filters = []): array
    {
        $query = DB::table('attendance_details as adet')
            ->join('attendance_sessions as asess', 'asess.attendance_session_id', '=', 'adet.attendance_session_id')
            ->join('schedules as sc', 'sc.schedule_id', '=', 'asess.schedule_id')
            ->join('grade_subjects as gs', 'gs.id', '=', 'sc.grade_subject_id')
            ->join('subjects as sub', 'sub.id', '=', 'gs.subject_id')
            ->join('teachers as t', 't.teacher_id', '=', 'asess.teacher_id')
            ->where('adet.student_id', $studentId);

        if ($semesterId) {
            $query->where('asess.semester_id', $semesterId);
        }

        if (! empty($filters['status'])) {
            $query->where('adet.status', $filters['status']);
        }

        if (! empty($filters['month'])) {
            $query->whereMonth('asess.attendance_date', $filters['month']);
        }

        return $query->select([
            'asess.attendance_date',
            'asess.start_time',
            'asess.end_time',
            'sub.subject_name as mapel',
            't.full_name as guru',
            'adet.status',
            'adet.notes',
        ])
            ->orderByDesc('asess.attendance_date')
            ->orderByDesc('asess.start_time')
            ->get()
            ->toArray();
    }

    /**
     * Get subject scores breakdown for child.
     */
    public function getScoreSubjects(int $studentId, ?int $gradeId, ?int $semesterId): array
    {
        if (! $gradeId || ! $semesterId) {
            return [];
        }

        return DB::table('score_details as sd')
            ->join('score_sessions as ss', 'ss.score_session_id', '=', 'sd.score_session_id')
            ->join('grade_subjects as gs', 'gs.id', '=', 'ss.grade_subject_id')
            ->join('subjects as sub', 'sub.id', '=', 'gs.subject_id')
            ->where('sd.student_id', $studentId)
            ->where('gs.grade_id', $gradeId)
            ->where('ss.semester_id', $semesterId)
            ->where('ss.is_published', true)
            ->groupBy('sub.id', 'sub.subject_name', 'sub.code')
            ->selectRaw('
                sub.id as subject_id,
                sub.subject_name,
                sub.code as subject_code,
                ROUND(AVG(sd.score), 1) as avg_score,
                MAX(sd.score) as max_score,
                MIN(sd.score) as min_score,
                COUNT(sd.score_detail_id) as total_assessments
            ')
            ->orderBy('sub.subject_name')
            ->get()
            ->toArray();
    }

    /**
     * Get score sessions breakdown.
     */
    public function getScoreSessions(int $studentId, ?int $gradeId, ?int $semesterId): array
    {
        if (! $gradeId || ! $semesterId) {
            return [];
        }

        return DB::table('score_details as sd')
            ->join('score_sessions as ss', 'ss.score_session_id', '=', 'sd.score_session_id')
            ->join('grade_subjects as gs', 'gs.id', '=', 'ss.grade_subject_id')
            ->join('subjects as sub', 'sub.id', '=', 'gs.subject_id')
            ->where('sd.student_id', $studentId)
            ->where('gs.grade_id', $gradeId)
            ->where('ss.semester_id', $semesterId)
            ->where('ss.is_published', true)
            ->select([
                'ss.session_name',
                'ss.session_type',
                'sub.subject_name as mapel',
                'sd.score',
                'ss.session_date',
            ])
            ->orderByDesc('ss.session_date')
            ->get()
            ->toArray();
    }

    /**
     * Get teacher contacts for grade.
     */
    public function getTeacherContacts(?int $gradeId): array
    {
        if (! $gradeId) {
            return [];
        }

        return DB::table('grade_subjects as gs')
            ->join('subjects as sub', 'sub.id', '=', 'gs.subject_id')
            ->join('teachers as t', 't.teacher_id', '=', 'gs.teacher_id')
            ->where('gs.grade_id', $gradeId)
            ->select([
                't.teacher_id',
                't.full_name as nama_guru',
                't.email',
                't.phone_number as hp',
                't.nip',
                'sub.subject_name as mapel',
            ])
            ->get()
            ->toArray();
    }
}
