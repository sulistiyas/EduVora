<?php

namespace App\Repositories\Reports\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AttendanceReportRepositoryInterface
{
    /**
     * Get paginated attendance sessions with filters.
     *
     * Filters:
     *   - teacher_id   (int)    required — scoped to logged-in teacher
     *   - school_id    (int)    required
     *   - semester_id  (int)    optional
     *   - grade_id     (int)    optional
     *   - subject_id   (int)    optional
     *   - status       (string) optional — H|I|S|A|L
     *   - date_from    (string) optional — Y-m-d
     *   - date_to      (string) optional — Y-m-d
     *
     * @param  array  $filters
     * @param  int    $perPage
     * @return LengthAwarePaginator
     */
    public function getSessions(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get all attendance details (per-student rows) for a given session.
     *
     * @param  int  $sessionId
     * @return Collection
     */
    public function getDetailsBySession(int $sessionId): Collection;

    /**
     * Get per-student attendance summary within filters.
     * Returns: student_id, full_name, nis, H, I, S, A, L counts, total_sessions.
     *
     * @param  array  $filters
     * @return Collection
     */
    public function getStudentSummary(array $filters): Collection;

    /**
     * Get aggregate summary counts for the filter scope.
     * Returns: total_sessions, total_present, total_permission,
     *          total_sick, total_absent, total_late.
     *
     * @param  array  $filters
     * @return array
     */
    public function getAggregateSummary(array $filters): array;

    /**
     * Get grades that belong to the teacher's sessions.
     * Used to populate the grade filter dropdown.
     *
     * @param  int  $teacherId
     * @param  int  $schoolId
     * @return Collection
     */
    public function getGradeOptions(int $teacherId, int $schoolId): Collection;

    /**
     * Get subjects taught by the teacher.
     * Used to populate the subject filter dropdown.
     *
     * @param  int  $teacherId
     * @param  int  $schoolId
     * @return Collection
     */
    public function getSubjectOptions(int $teacherId, int $schoolId): Collection;

    /**
     * Get all sessions (no pagination) for export purposes.
     *
     * @param  array  $filters
     * @return Collection
     */
    // public function getSessionsForExport(array $filters): Collection;
}