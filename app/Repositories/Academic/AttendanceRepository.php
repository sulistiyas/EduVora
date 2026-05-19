<?php

namespace App\Repositories\Academic;

use App\Models\Activity\AttendanceDetail;
use App\Models\Activity\AttendanceSession;
use App\Models\Student\Student;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AttendanceRepository
{
    /*
    |--------------------------------------------------------------------------
    | SESSION — queries
    |--------------------------------------------------------------------------
    */

    /**
     * Paginate attendance sessions for a specific teacher.
     */
    public function paginateByTeacher(
        int   $teacherId,
        array $filters  = [],
        int   $perPage  = 15
    ): LengthAwarePaginator {
        return AttendanceSession::with([
                'schedule.gradeSubject.subject',
                'grade',
                'subject',
                'semester',
            ])
            ->where('teacher_id', $teacherId)
            ->when(
                ! empty($filters['semester_id']),
                fn ($q) => $q->where('semester_id', $filters['semester_id'])
            )
            ->when(
                ! empty($filters['grade_id']),
                fn ($q) => $q->where('grade_id', $filters['grade_id'])
            )
            ->when(
                ! empty($filters['subject_id']),
                fn ($q) => $q->where('subject_id', $filters['subject_id'])
            )
            ->when(
                ! empty($filters['status']),
                fn ($q) => $q->where('status', $filters['status'])
            )
            ->when(
                ! empty($filters['date_from']),
                fn ($q) => $q->whereDate('attendance_date', '>=', $filters['date_from'])
            )
            ->when(
                ! empty($filters['date_to']),
                fn ($q) => $q->whereDate('attendance_date', '<=', $filters['date_to'])
            )
            ->orderByDesc('attendance_date')
            ->orderByDesc('attendance_session_id')
            ->paginate($perPage);
    }

    /**
     * Find a session by ID with full relations.
     */
    public function findById(int $id): ?AttendanceSession
    {
        return AttendanceSession::with([
            'schedule',
            'teacher',
            'subject',
            'grade',
            'semester',
            'recorder',
            'details.student',
        ])->find($id);
    }

    /**
     * Find an existing session for a specific schedule on a given date.
     */
    public function findByScheduleAndDate(int $scheduleId, string $date): ?AttendanceSession
    {
        return AttendanceSession::with(['details.student'])
            ->where('schedule_id',      $scheduleId)
            ->whereDate('attendance_date', $date)
            ->first();
    }

    /**
     * Create a new attendance session.
     */
    public function createSession(array $data): AttendanceSession
    {
        return AttendanceSession::create($data);
    }

    /**
     * Update session fields (notes, status, is_locked, meeting_number).
     */
    public function updateSession(AttendanceSession $session, array $data): AttendanceSession
    {
        $session->update($data);
        return $session->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | DETAILS — queries
    |--------------------------------------------------------------------------
    */

    /**
     * Bulk-insert attendance details for a session.
     * Uses upsert to handle re-submission gracefully.
     */
    public function upsertDetails(int $sessionId, array $details): void
    {
        $rows = array_map(fn ($d) => [
            'attendance_session_id' => $sessionId,
            'student_id'            => $d['student_id'],
            'status'                => $d['status'],
            'note'                  => $d['note']       ?? null,
            'attachment'            => $d['attachment'] ?? null,
            'created_at'            => now(),
            'updated_at'            => now(),
        ], $details);

        AttendanceDetail::upsert(
            $rows,
            uniqueBy: ['attendance_session_id', 'student_id'],
            update:   ['status', 'note', 'attachment', 'updated_at']
        );
    }

    /**
     * Get all details for a session (with student relation).
     */
    public function getDetailsBySession(int $sessionId): Collection
    {
        return AttendanceDetail::with('student')
            ->where('attendance_session_id', $sessionId)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | STUDENTS — helper
    |--------------------------------------------------------------------------
    */

    /**
     * Get all active students in a grade, ordered by name.
     */
    public function getStudentsByGrade(int $gradeId): Collection
    {
        return Student::where('grade_id', $gradeId)
            // ->where('status', 'active')
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'nis']);
    }

    /*
    |--------------------------------------------------------------------------
    | STATS — for dashboard / index summary
    |--------------------------------------------------------------------------
    */

    /**
     * Count sessions by status for a teacher in a semester.
     */
    public function countByStatus(int $teacherId, ?int $semesterId = null): array
    {
        $rows = AttendanceSession::where('teacher_id', $teacherId)
                ->when(
                    $semesterId,
                    fn ($q) => $q->where('semester_id', $semesterId)
                )
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

        return [
            'draft'     => $rows[AttendanceSession::STATUS_DRAFT]     ?? 0,
            'submitted' => $rows[AttendanceSession::STATUS_SUBMITTED]  ?? 0,
            'approved'  => $rows[AttendanceSession::STATUS_APPROVED]   ?? 0,
            'total'     => $rows->sum(),
        ];
    }
}