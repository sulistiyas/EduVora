<?php

namespace App\Repositories\Reports\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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
     */
    public function getSessions(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get all attendance details (per-student rows) for a given session.
     */
    public function getDetailsBySession(int $sessionId): Collection;

    /**
     * Get per-student attendance summary within filters.
     * Returns: student_id, full_name, nis, H, I, S, A, L counts, total_sessions.
     */
    public function getStudentSummary(array $filters): Collection;

    /**
     * Get aggregate summary counts for the filter scope.
     * Returns: total_sessions, total_present, total_permission,
     *          total_sick, total_absent, total_late.
     */
    public function getAggregateSummary(array $filters): array;

    /**
     * Get grades that belong to the teacher's sessions.
     * Used to populate the grade filter dropdown.
     */
    public function getGradeOptions(int $teacherId, int $schoolId): Collection;

    /**
     * Get subjects taught by the teacher.
     * Used to populate the subject filter dropdown.
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
