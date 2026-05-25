<?php

namespace App\Repositories\Reports\Teacher;

use App\Models\Activity\AttendanceDetail;
use App\Models\Activity\AttendanceSession;
use App\Repositories\Reports\Contracts\AttendanceReportRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttendanceReportRepository implements AttendanceReportRepositoryInterface
{
    /*
    |--------------------------------------------------------------------------
    | SESSIONS (paginated)
    |--------------------------------------------------------------------------
    */

    /**
     * {@inheritdoc}
     */
    public function getSessions(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return AttendanceSession::query()
            ->with([
                'grade:grade_id,grade_name,level',
                'subject:id,subject_name,subject_code',
                'semester:semester_id,semester_name',
                'details',
            ])
            ->when(
                isset($filters['teacher_id']),
                fn ($q) => $q->byTeacher($filters['teacher_id'])
            )
            ->when(
                isset($filters['school_id']),
                fn ($q) => $q->bySchool($filters['school_id'])
            )
            ->when(
                !empty($filters['semester_id']),
                fn ($q) => $q->where('semester_id', $filters['semester_id'])
            )
            ->when(
                !empty($filters['grade_id']),
                fn ($q) => $q->byGrade($filters['grade_id'])
            )
            ->when(
                !empty($filters['subject_id']),
                fn ($q) => $q->where('subject_id', $filters['subject_id'])
            )
            ->when(
                !empty($filters['status']),
                fn ($q) => $q->whereHas(
                    'details',
                    fn ($d) => $d->where('status', $filters['status'])
                )
            )
            ->when(
                !empty($filters['date_from']),
                fn ($q) => $q->whereDate('attendance_date', '>=', $filters['date_from'])
            )
            ->when(
                !empty($filters['date_to']),
                fn ($q) => $q->whereDate('attendance_date', '<=', $filters['date_to'])
            )
            ->orderByDesc('attendance_date')
            ->paginate($perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | SESSION DETAIL
    |--------------------------------------------------------------------------
    */

    /**
     * {@inheritdoc}
     */
    public function getDetailsBySession(int $sessionId): Collection
    {
        return AttendanceDetail::query()
            ->with([
                'student:id,full_name,nis,class_group',
                'session:attendance_session_id,meeting_number',
            ])
            ->where('attendance_session_id', $sessionId)
            ->orderBy('student_id')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | PER-STUDENT SUMMARY
    |--------------------------------------------------------------------------
    */

    /**
     * {@inheritdoc}
     */
    public function getStudentSummary(array $filters): Collection
    {
        return AttendanceDetail::query()
            ->join(
                'attendance_sessions',
                'attendance_details.attendance_session_id',
                '=',
                'attendance_sessions.attendance_session_id'
            )
            ->join(
                'students',
                'attendance_details.student_id',
                '=',
                'students.id'
            )
            ->select([
                'students.id as student_id',
                'students.full_name',
                'students.nis',
                DB::raw("SUM(CASE WHEN attendance_details.status = 'H' THEN 1 ELSE 0 END) as total_present"),
                DB::raw("SUM(CASE WHEN attendance_details.status = 'I' THEN 1 ELSE 0 END) as total_permission"),
                DB::raw("SUM(CASE WHEN attendance_details.status = 'S' THEN 1 ELSE 0 END) as total_sick"),
                DB::raw("SUM(CASE WHEN attendance_details.status = 'A' THEN 1 ELSE 0 END) as total_absent"),
                DB::raw("SUM(CASE WHEN attendance_details.status = 'L' THEN 1 ELSE 0 END) as total_late"),
                DB::raw('COUNT(attendance_details.attendance_detail_id) as total_sessions'),
            ])
            ->when(
                isset($filters['teacher_id']),
                fn ($q) => $q->where('attendance_sessions.teacher_id', $filters['teacher_id'])
            )
            ->when(
                isset($filters['school_id']),
                fn ($q) => $q->where('attendance_sessions.school_id', $filters['school_id'])
            )
            ->when(
                !empty($filters['semester_id']),
                fn ($q) => $q->where('attendance_sessions.semester_id', $filters['semester_id'])
            )
            ->when(
                !empty($filters['grade_id']),
                fn ($q) => $q->where('attendance_sessions.grade_id', $filters['grade_id'])
            )
            ->when(
                !empty($filters['subject_id']),
                fn ($q) => $q->where('attendance_sessions.subject_id', $filters['subject_id'])
            )
            ->when(
                !empty($filters['date_from']),
                fn ($q) => $q->whereDate('attendance_sessions.attendance_date', '>=', $filters['date_from'])
            )
            ->when(
                !empty($filters['date_to']),
                fn ($q) => $q->whereDate('attendance_sessions.attendance_date', '<=', $filters['date_to'])
            )
            ->groupBy('students.id', 'students.full_name', 'students.nis')
            ->orderBy('students.full_name')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | AGGREGATE SUMMARY (untuk summary cards)
    |--------------------------------------------------------------------------
    */

    /**
     * {@inheritdoc}
     */
    public function getAggregateSummary(array $filters): array
    {
        $row = AttendanceDetail::query()
            ->join(
                'attendance_sessions',
                'attendance_details.attendance_session_id',
                '=',
                'attendance_sessions.attendance_session_id'
            )
            ->select([
                DB::raw("SUM(CASE WHEN attendance_details.status = 'H' THEN 1 ELSE 0 END) as total_present"),
                DB::raw("SUM(CASE WHEN attendance_details.status = 'I' THEN 1 ELSE 0 END) as total_permission"),
                DB::raw("SUM(CASE WHEN attendance_details.status = 'S' THEN 1 ELSE 0 END) as total_sick"),
                DB::raw("SUM(CASE WHEN attendance_details.status = 'A' THEN 1 ELSE 0 END) as total_absent"),
                DB::raw("SUM(CASE WHEN attendance_details.status = 'L' THEN 1 ELSE 0 END) as total_late"),
                DB::raw('COUNT(DISTINCT attendance_sessions.attendance_session_id) as total_sessions'),
            ])
            ->when(
                isset($filters['teacher_id']),
                fn ($q) => $q->where('attendance_sessions.teacher_id', $filters['teacher_id'])
            )
            ->when(
                isset($filters['school_id']),
                fn ($q) => $q->where('attendance_sessions.school_id', $filters['school_id'])
            )
            ->when(
                !empty($filters['semester_id']),
                fn ($q) => $q->where('attendance_sessions.semester_id', $filters['semester_id'])
            )
            ->when(
                !empty($filters['grade_id']),
                fn ($q) => $q->where('attendance_sessions.grade_id', $filters['grade_id'])
            )
            ->when(
                !empty($filters['subject_id']),
                fn ($q) => $q->where('attendance_sessions.subject_id', $filters['subject_id'])
            )
            ->when(
                !empty($filters['date_from']),
                fn ($q) => $q->whereDate('attendance_sessions.attendance_date', '>=', $filters['date_from'])
            )
            ->when(
                !empty($filters['date_to']),
                fn ($q) => $q->whereDate('attendance_sessions.attendance_date', '<=', $filters['date_to'])
            )
            ->first();

        return [
            'total_sessions'    => (int) ($row->total_sessions    ?? 0),
            'total_present'     => (int) ($row->total_present     ?? 0),
            'total_permission'  => (int) ($row->total_permission  ?? 0),
            'total_sick'        => (int) ($row->total_sick        ?? 0),
            'total_absent'      => (int) ($row->total_absent      ?? 0),
            'total_late'        => (int) ($row->total_late        ?? 0),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | DROPDOWN OPTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * {@inheritdoc}
     */
    public function getGradeOptions(int $teacherId, int $schoolId): Collection
    {
        return AttendanceSession::query()
            ->with('grade:grade_id,grade_name,level')
            ->where('teacher_id', $teacherId)
            ->where('school_id', $schoolId)
            ->select('grade_id')
            ->distinct()
            ->get()
            ->pluck('grade')
            ->filter()
            ->sortBy('grade_name')
            ->values();
    }

    /**
     * {@inheritdoc}
     */
    public function getSubjectOptions(int $teacherId, int $schoolId): Collection
    {
        return AttendanceSession::query()
            ->with('subject:id,subject_name,subject_code')
            ->where('teacher_id', $teacherId)
            ->where('school_id', $schoolId)
            ->select('subject_id')
            ->distinct()
            ->get()
            ->pluck('subject')
            ->filter()
            ->sortBy('subject_name')
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT (no pagination)
    |--------------------------------------------------------------------------
    */

    /**
     * {@inheritdoc}
     */
    // public function getSessionsForExport(array $filters): Collection
    // {
    //     return AttendanceSession::query()
    //         ->with([
    //             'grade:grade_id,grade_name,level',
    //             'subject:id,subject_name,subject_code',
    //             'semester:semester_id,semester_name',
    //             'details.student:id,full_name,nis',
    //         ])
    //         ->when(
    //             isset($filters['teacher_id']),
    //             fn ($q) => $q->byTeacher($filters['teacher_id'])
    //         )
    //         ->when(
    //             isset($filters['school_id']),
    //             fn ($q) => $q->bySchool($filters['school_id'])
    //         )
    //         ->when(
    //             !empty($filters['semester_id']),
    //             fn ($q) => $q->where('semester_id', $filters['semester_id'])
    //         )
    //         ->when(
    //             !empty($filters['grade_id']),
    //             fn ($q) => $q->byGrade($filters['grade_id'])
    //         )
    //         ->when(
    //             !empty($filters['subject_id']),
    //             fn ($q) => $q->where('subject_id', $filters['subject_id'])
    //         )
    //         ->when(
    //             !empty($filters['date_from']),
    //             fn ($q) => $q->whereDate('attendance_date', '>=', $filters['date_from'])
    //         )
    //         ->when(
    //             !empty($filters['date_to']),
    //             fn ($q) => $q->whereDate('attendance_date', '<=', $filters['date_to'])
    //         )
    //         ->orderByDesc('attendance_date')
    //         ->get();
    // }
}