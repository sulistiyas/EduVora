<?php

namespace App\Repositories\Reports\Teacher;

use App\Models\Academic\GradeSubject;
use App\Models\Academic\Semester;
use App\Models\Student\ScoreDetail;
use App\Models\Student\ScoreSession;
use App\Repositories\Reports\Contracts\ScoreReportRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ScoreReportRepository implements ScoreReportRepositoryInterface
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
        return ScoreSession::query()
            ->with([
                'gradeSubject.grade:grade_id,grade_name,level',
                'gradeSubject.subject:id,subject_name,subject_code',
                'semester:semester_id,semester_name',
                'scoreDetails',
            ])
            ->withAvg('scoreDetails as avg_score', 'score')
            ->withMax('scoreDetails as highest_score', 'score')
            ->withMin('scoreDetails as lowest_score', 'score')
            ->withCount([
                'scoreDetails as below_kkm_count' => fn ($q) => $q->where('score', '<', 75),
            ])
            ->whereHas(
                'gradeSubject',
                fn ($q) => $q->where('teacher_id', $filters['teacher_id'])
            )
            ->when(
                ! empty($filters['semester_id']),
                fn ($q) => $q->where('semester_id', $filters['semester_id'])
            )
            ->when(
                ! empty($filters['grade_id']),
                fn ($q) => $q->whereHas(
                    'gradeSubject',
                    fn ($q) => $q->where('grade_id', $filters['grade_id'])
                )
            )
            ->when(
                ! empty($filters['subject_id']),
                fn ($q) => $q->whereHas(
                    'gradeSubject',
                    fn ($q) => $q->where('subject_id', $filters['subject_id'])
                )
            )
            ->when(
                ! empty($filters['score_type']),
                fn ($q) => $q->where('score_type', $filters['score_type'])
            )
            ->when(
                isset($filters['is_published']),
                fn ($q) => $q->where('is_published', $filters['is_published'])
            )
            ->orderByDesc('score_date')
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
        return ScoreDetail::query()
            ->with([
                'student:id,full_name,nis,class_group',
                'scoreSession:score_session_id,title,score_type,score_date',
            ])
            ->where('score_session_id', $sessionId)
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
        /*
         * Subquery each score type separately, then join to students.
         * This avoids messy GROUP BY with CASE-inside-AVG patterns and
         * makes the weighted final score straightforward to compute.
         */
        $gradeSubjectId = $this->resolveGradeSubjectId($filters);

        if (! $gradeSubjectId) {
            return collect();
        }

        $gradeSubject = GradeSubject::find($gradeSubjectId);

        return ScoreDetail::query()
            ->join(
                'score_sessions',
                'score_details.score_session_id',
                '=',
                'score_sessions.score_session_id'
            )
            ->join(
                'students',
                'score_details.student_id',
                '=',
                'students.id'
            )
            ->join(
                'grade_subjects',
                'score_sessions.grade_subject_id',
                '=',
                'grade_subjects.id'
            )
            ->select([
                'students.id   as student_id',
                'students.full_name',
                'students.nis',
                'grade_subjects.kkm',
                'grade_subjects.weight_harian',
                'grade_subjects.weight_uts',
                'grade_subjects.weight_uas',
                DB::raw("AVG(CASE WHEN score_sessions.score_type = 'daily'      THEN score_details.score END) as avg_harian"),
                DB::raw("AVG(CASE WHEN score_sessions.score_type = 'mid_exam'   THEN score_details.score END) as avg_uts"),
                DB::raw("AVG(CASE WHEN score_sessions.score_type = 'final_exam' THEN score_details.score END) as avg_uas"),
            ])
            ->where('score_sessions.grade_subject_id', $gradeSubjectId)
            ->when(
                ! empty($filters['semester_id']),
                fn ($q) => $q->where('score_sessions.semester_id', $filters['semester_id'])
            )
            ->groupBy(
                'students.id',
                'students.full_name',
                'students.nis',
                'grade_subjects.kkm',
                'grade_subjects.weight_harian',
                'grade_subjects.weight_uts',
                'grade_subjects.weight_uas',
            )
            ->orderBy('students.full_name')
            ->get()
            ->map(function ($row) {
                $totalWeight = $row->weight_harian + $row->weight_uts + $row->weight_uas;

                $finalScore = $totalWeight > 0
                    ? (
                        (($row->avg_harian ?? 0) * $row->weight_harian) +
                        (($row->avg_uts ?? 0) * $row->weight_uts) +
                        (($row->avg_uas ?? 0) * $row->weight_uas)
                    ) / $totalWeight
                    : 0;

                $row->final_score = round($finalScore, 2);
                $row->is_below_kkm = $finalScore < $row->kkm;

                return $row;
            });
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
        $gradeSubjectId = $this->resolveGradeSubjectId($filters);

        if (! $gradeSubjectId) {
            return $this->emptyAggregateSummary();
        }

        $gradeSubject = GradeSubject::find($gradeSubjectId);
        $kkm = $gradeSubject?->kkm ?? 0;

        $row = ScoreDetail::query()
            ->join(
                'score_sessions',
                'score_details.score_session_id',
                '=',
                'score_sessions.score_session_id'
            )
            ->select([
                DB::raw('COUNT(DISTINCT score_sessions.score_session_id) as total_sessions'),
                DB::raw('ROUND(AVG(score_details.score), 2)              as class_avg'),
                DB::raw('MAX(score_details.score)                        as highest_score'),
                DB::raw('MIN(score_details.score)                        as lowest_score'),
            ])
            ->where('score_sessions.grade_subject_id', $gradeSubjectId)
            ->when(
                ! empty($filters['semester_id']),
                fn ($q) => $q->where('score_sessions.semester_id', $filters['semester_id'])
            )
            ->when(
                ! empty($filters['score_type']),
                fn ($q) => $q->where('score_sessions.score_type', $filters['score_type'])
            )
            ->first();

        // Count students below KKM using final weighted score
        $belowKkmCount = $this->getStudentSummary($filters)
            ->where('is_below_kkm', true)
            ->count();

        return [
            'total_sessions' => (int) ($row->total_sessions ?? 0),
            'class_avg' => (float) ($row->class_avg ?? 0),
            'highest' => (float) ($row->highest_score ?? 0),
            'lowest' => (float) ($row->lowest_score ?? 0),
            'below_kkm_count' => $belowKkmCount,
            'kkm' => $kkm,
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
    public function getGradeSubjectOptions(int $teacherId, int $schoolId): Collection
    {
        return GradeSubject::query()
            ->with([
                'grade:grade_id,grade_name,level',
                'subject:id,subject_name,subject_code',
            ])
            ->where('teacher_id', $teacherId)
            ->where('status', 'active')
            ->orderBy('grade_id')
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getSemesterOptions(int $teacherId, int $schoolId): Collection
    {
        return Semester::query()
            ->whereIn('semester_id', function ($sub) use ($teacherId) {
                $sub->select('semester_id')
                    ->from('score_sessions')
                    ->join(
                        'grade_subjects',
                        'score_sessions.grade_subject_id',
                        '=',
                        'grade_subjects.id'
                    )
                    ->where('grade_subjects.teacher_id', $teacherId);
            })
            ->orderByDesc('start_date')
            ->get(['semester_id', 'semester_name', 'status']);
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT (no pagination)
    |--------------------------------------------------------------------------
    */

    /**
     * {@inheritdoc}
     */
    public function getStudentSummaryForExport(array $filters): Collection
    {
        // Reuse getStudentSummary — no pagination needed there already
        return $this->getStudentSummary($filters);
    }

    /**
     * {@inheritdoc}
     */
    public function getSessionsForExport(array $filters): Collection
    {
        $gradeSubjectId = $this->resolveGradeSubjectId($filters);

        return ScoreSession::query()
            ->with([
                'gradeSubject.grade:grade_id,grade_name,level',
                'gradeSubject.subject:id,subject_name,subject_code',
                'semester:semester_id,semester_name',
                'scoreDetails.student:id,full_name,nis',
            ])
            ->when(
                $gradeSubjectId,
                fn ($q) => $q->where('grade_subject_id', $gradeSubjectId)
            )
            ->when(
                ! empty($filters['semester_id']),
                fn ($q) => $q->where('semester_id', $filters['semester_id'])
            )
            ->when(
                ! empty($filters['score_type']),
                fn ($q) => $q->where('score_type', $filters['score_type'])
            )
            ->orderByDesc('score_date')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Resolve grade_subject_id from filters.
     * Accepts direct grade_subject_id, or derive from grade_id + subject_id + teacher_id.
     */
    private function resolveGradeSubjectId(array $filters): ?int
    {
        if (! empty($filters['grade_subject_id'])) {
            return (int) $filters['grade_subject_id'];
        }

        if (! empty($filters['grade_id']) && ! empty($filters['subject_id'])) {
            return GradeSubject::query()
                ->where('grade_id', $filters['grade_id'])
                ->where('subject_id', $filters['subject_id'])
                ->where('teacher_id', $filters['teacher_id'])
                ->value('id');
        }

        // Fallback: first grade_subject owned by this teacher
        // return GradeSubject::query()
        //     ->where('teacher_id', $filters['teacher_id'])
        //     ->value('id');

        return null;
    }

    private function emptyAggregateSummary(): array
    {
        return [
            'total_sessions' => 0,
            'class_avg' => 0.0,
            'highest' => 0.0,
            'lowest' => 0.0,
            'below_kkm_count' => 0,
            'kkm' => 0,
        ];
    }
}
