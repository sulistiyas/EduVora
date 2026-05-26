<?php

namespace App\Repositories\Reports\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ScoreReportRepositoryInterface
{
    /**
     * Get paginated score sessions with filters.
     *
     * Filters:
     *   - teacher_id   (int)    required — scoped to logged-in teacher
     *   - school_id    (int)    required
     *   - semester_id  (int)    optional
     *   - grade_id     (int)    optional
     *   - score_type   (string) optional — harian|uts|uas
     *
     * @param  array  $filters
     * @param  int    $perPage
     * @return LengthAwarePaginator
     */
    public function getSessions(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get all score details (per-student rows) for a given session.
     *
     * @param  int  $sessionId
     * @return Collection
     */
    public function getDetailsBySession(int $sessionId): Collection;

    /**
     * Get per-student score summary within filters.
     *
     * Returns per student:
     *   - student_id, full_name, nis
     *   - avg_harian, avg_uts, avg_uas
     *   - final_score  (weighted: weight_harian, weight_uts, weight_uas dari grade_subjects)
     *   - kkm          (dari grade_subjects)
     *   - is_below_kkm (boolean)
     *
     * @param  array  $filters
     * @return Collection
     */
    public function getStudentSummary(array $filters): Collection;

    /**
     * Get aggregate summary for summary cards.
     *
     * Returns:
     *   - total_sessions  (int)
     *   - class_avg       (float)
     *   - highest_score   (float)
     *   - lowest_score    (float)
     *   - below_kkm_count (int)
     *
     * @param  array  $filters
     * @return array
     */
    public function getAggregateSummary(array $filters): array;

    /**
     * Get grade_subjects belonging to the teacher.
     * Used to populate grade + subject filter dropdowns.
     *
     * @param  int  $teacherId
     * @param  int  $schoolId
     * @return Collection
     */
    public function getGradeSubjectOptions(int $teacherId, int $schoolId): Collection;

    /**
     * Get semesters that have score sessions for the teacher.
     * Used to populate semester filter dropdown.
     *
     * @param  int  $teacherId
     * @param  int  $schoolId
     * @return Collection
     */
    public function getSemesterOptions(int $teacherId, int $schoolId): Collection;

    /**
     * Get all student summary rows (no pagination) for export.
     *
     * @param  array  $filters
     * @return Collection
     */
    public function getStudentSummaryForExport(array $filters): Collection;

    /**
     * Get all sessions (no pagination) for export.
     *
     * @param  array  $filters
     * @return Collection
     */
    public function getSessionsForExport(array $filters): Collection;
}