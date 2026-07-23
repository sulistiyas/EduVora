<?php

namespace App\Services\Reports\Teacher;

use App\Models\Student\ScoreSession;
use App\Repositories\Reports\Contracts\ScoreReportRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ScoreReportService
{
    public function __construct(
        protected ScoreReportRepositoryInterface $repo,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | FILTER BUILDER
    | Sanitize & normalize raw request input menjadi filter array standar
    |--------------------------------------------------------------------------
    */

    /**
     * Build a clean filter array from raw request input.
     * Selalu inject teacher_id & school_id dari auth context.
     *
     * @param  array  $input  — dari $request->only([...])
     * @param  int  $teacherId  — dari auth teacher
     * @param  int  $schoolId  — dari auth school
     */
    public function buildFilters(array $input, int $teacherId, int $schoolId): array
    {
        return [
            'teacher_id' => $teacherId,
            'school_id' => $schoolId,
            'semester_id' => $input['semester_id'] ?? null,
            'grade_id' => $input['grade_id'] ?? null,
            'subject_id' => $input['subject_id'] ?? null,
            'grade_subject_id' => $input['grade_subject_id'] ?? null,
            'score_type' => $this->sanitizeScoreType($input['score_type'] ?? null),
            'is_published' => isset($input['is_published'])
                                    ? (bool) $input['is_published']
                                    : null,
        ];
    }

    /**
     * Validate score_type value against allowed constants.
     */
    private function sanitizeScoreType(?string $type): ?string
    {
        $allowed = [
            ScoreSession::TYPE_DAILY, // 'daily'
            ScoreSession::TYPE_MID_EXAM,    // 'mid_exam'
            ScoreSession::TYPE_FINAL_EXAM,    // 'final_exam'
            ScoreSession::TYPE_ASSIGNMENT,    // 'assignment'
        ];

        return in_array($type, $allowed, true) ? $type : null;
    }

    /*
    |--------------------------------------------------------------------------
    | PAGE DATA
    | Single method untuk index page — ambil semua data yang dibutuhkan view
    |--------------------------------------------------------------------------
    */

    /**
     * Get all data needed for the score report index page.
     *
     * Returns:
     *   - sessions        : LengthAwarePaginator  (tab Daftar Sesi)
     *   - student_summary : Collection            (tab Rekap Per Siswa)
     *   - summary         : array                 (aggregate counts untuk cards)
     *   - grade_subjects  : Collection            (dropdown options)
     *   - grades          : Collection            (dropdown options)
     *   - subjects        : Collection            (dropdown options)
     *   - semesters       : Collection            (dropdown options)
     *   - score_types     : array                 (toggle options)
     *   - filters         : array                 (active filters — dikirim balik ke view)
     */
    public function getIndexData(array $filters, int $perPage = 15): array
    {
        $filterOptions = $this->repo->getGradeSubjectOptions(
            $filters['teacher_id'],
            $filters['school_id'],
        );

        return [
            'sessions' => $this->repo->getSessions($filters, $perPage),
            'student_summary' => $this->getFormattedStudentSummary($filters),
            'summary' => $this->getFormattedSummary($filters),
            'grade_subjects' => $filterOptions,
            'grades' => $this->extractGrades($filterOptions),
            'subjects' => $this->extractSubjects($filterOptions),
            'semesters' => $this->repo->getSemesterOptions(
                $filters['teacher_id'],
                $filters['school_id'],
            ),
            'score_types' => $this->getScoreTypeOptions(),
            'filters' => $filters,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | SUMMARY CARDS
    |--------------------------------------------------------------------------
    */

    /**
     * Get aggregate summary formatted untuk summary cards.
     * Adds: pass_rate, below_kkm_rate — siap render di view.
     */
    public function getFormattedSummary(array $filters): array
    {
        $raw = $this->repo->getAggregateSummary($filters);
        $totalStudents = $this->repo->getStudentSummary($filters)->count();

        $raw['total_students'] = $totalStudents;
        $raw['pass_rate'] = $totalStudents > 0
            ? round((($totalStudents - $raw['below_kkm_count']) / $totalStudents) * 100, 1)
            : 0;
        $raw['below_kkm_rate'] = $totalStudents > 0
            ? round(($raw['below_kkm_count'] / $totalStudents) * 100, 1)
            : 0;
        $raw['class_avg_fmt'] = number_format($raw['class_avg'], 1);
        $raw['highest_fmt'] = number_format($raw['highest'], 1);
        $raw['lowest_fmt'] = number_format($raw['lowest'], 1);
        $raw['has_below_kkm'] = $raw['below_kkm_count'] > 0;

        return $raw;
    }

    /*
    |--------------------------------------------------------------------------
    | STUDENT SUMMARY (tab Rekap Per Siswa)
    |--------------------------------------------------------------------------
    */

    /**
     * Get per-student summary with formatted display values.
     */
    public function getFormattedStudentSummary(array $filters): Collection
    {
        return $this->repo
            ->getStudentSummary($filters)
            ->map(fn ($row) => $this->formatStudentRow($row));
    }

    /*
    |--------------------------------------------------------------------------
    | SESSION DETAIL (modal)
    |--------------------------------------------------------------------------
    */

    /**
     * Get detail rows for a single session.
     * Returns raw collection + aggregate counts untuk modal.
     */
    public function getSessionDetail(int $sessionId): array
    {
        $details = $this->repo->getDetailsBySession($sessionId);

        $scores = $details->pluck('score')->filter();

        return [
            'details' => $details,
            'counts' => [
                'total' => $details->count(),
                'above_kkm' => $details->filter(fn ($d) => $d->scoreSession?->gradeSubject?->kkm !== null
                                    && $d->score >= $d->scoreSession->gradeSubject->kkm)->count(),
                'below_kkm' => $details->filter(fn ($d) => $d->scoreSession?->gradeSubject?->kkm !== null
                                    && $d->score < $d->scoreSession->gradeSubject->kkm)->count(),
            ],
            'stats' => [
                'avg' => $scores->isNotEmpty() ? round($scores->avg(), 1) : 0,
                'highest' => $scores->isNotEmpty() ? $scores->max() : 0,
                'lowest' => $scores->isNotEmpty() ? $scores->min() : 0,
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT
    |--------------------------------------------------------------------------
    */

    /**
     * Get student summary rows formatted untuk Excel export.
     */
    public function getExportStudentSummary(array $filters): Collection
    {
        // if (empty($filters['grade_subject_id']) && empty($filters['grade_id'])) {
        //     return collect();
        // }
        $gradeSubjectId = $filters['grade_subject_id'] ?? null;
        if (! $gradeSubjectId && empty($filters['grade_id'])) {
            return collect();
        }

        return $this->repo
            ->getStudentSummaryForExport($filters)
            ->map(fn ($row) => $this->formatStudentRow($row))
            ->map(fn ($row) => [
                'no' => null,
                'nis' => $row->nis,
                'full_name' => $row->full_name,
                'avg_harian' => $row->avg_harian_fmt,
                'avg_uts' => $row->avg_uts_fmt,
                'avg_uas' => $row->avg_uas_fmt,
                'final_score' => $row->final_score_fmt,
                'kkm' => $row->kkm,
                'kkm_status' => $row->kkm_status_label,
            ]);
    }

    /**
     * Get sessions grouped by score_type untuk multi-sheet Excel export.
     *
     * @return array ['harian' => Collection, 'uts' => Collection, 'uas' => Collection]
     */
    public function getExportSessionsByType(array $filters): array
    {
        $sessions = $this->repo->getSessionsForExport($filters);

        return [
            'daily' => $sessions->where('score_type', ScoreSession::TYPE_DAILY)->values(),
            'assignment' => $sessions->where('score_type', ScoreSession::TYPE_ASSIGNMENT)->values(),
            'mid_exam' => $sessions->where('score_type', ScoreSession::TYPE_MID_EXAM)->values(),
            'final_exam' => $sessions->where('score_type', ScoreSession::TYPE_FINAL_EXAM)->values(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Score type options untuk toggle di view.
     */
    public function getScoreTypeOptions(): array
    {
        return [
            ['value' => ScoreSession::TYPE_DAILY,       'label' => 'Harian'],
            ['value' => ScoreSession::TYPE_ASSIGNMENT,  'label' => 'Tugas'],
            ['value' => ScoreSession::TYPE_MID_EXAM,    'label' => 'UTS'],
            ['value' => ScoreSession::TYPE_FINAL_EXAM,  'label' => 'UAS'],
        ];
    }

    /**
     * Format a raw student summary row — tambah _fmt suffix & status label.
     */
    private function formatStudentRow(object $row): object
    {
        $row->avg_harian_fmt = $row->avg_harian !== null ? number_format($row->avg_harian, 1) : '-';
        $row->avg_uts_fmt = $row->avg_uts !== null ? number_format($row->avg_uts, 1) : '-';
        $row->avg_uas_fmt = $row->avg_uas !== null ? number_format($row->avg_uas, 1) : '-';
        $row->final_score_fmt = number_format($row->final_score, 1);

        $row->kkm_status_label = $row->is_below_kkm ? 'Di bawah KKM' : 'Lulus KKM';
        $row->kkm_status_class = $row->is_below_kkm ? 'below-kkm' : 'pass-kkm';

        return $row;
    }

    /**
     * Extract unique sorted grades dari grade_subjects collection.
     */
    private function extractGrades(Collection $gradeSubjects): Collection
    {
        return $gradeSubjects
            ->map(fn ($gs) => $gs->grade)
            ->filter()
            ->unique('grade_id')
            ->sortBy('grade_name')
            ->values();
    }

    /**
     * Extract unique sorted subjects dari grade_subjects collection.
     */
    private function extractSubjects(Collection $gradeSubjects): Collection
    {
        return $gradeSubjects
            ->map(fn ($gs) => $gs->subject)
            ->filter()
            ->unique('id')
            ->sortBy('subject_name')
            ->values();
    }
}
