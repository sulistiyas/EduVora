<?php

namespace App\Repositories\School;

use App\Models\Student\ScoreDetail;
use App\Models\Student\ScoreSession;
use App\Models\Student\Student;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class StudentScoreRepository
{
    /*
    |--------------------------------------------------------------------------
    | SESSION — queries
    |--------------------------------------------------------------------------
    */

    /**
     * Paginate score sessions for a specific teacher.
     */
    public function paginateByTeacher(
        int   $teacherId,
        array $filters = [],
        int   $perPage = 15
    ): LengthAwarePaginator {
        return ScoreSession::with([
                'gradeSubject.subject',
                'gradeSubject.grade',
                'semester',
                'scoreDetails',
            ])
            ->whereHas('gradeSubject', fn ($q) => $q->where('teacher_id', $teacherId))
            ->when(
                ! empty($filters['semester_id']),
                fn ($q) => $q->where('semester_id', $filters['semester_id'])
            )
            ->when(
                ! empty($filters['grade_subject_id']),
                fn ($q) => $q->where('grade_subject_id', $filters['grade_subject_id'])
            )
            ->when(
                ! empty($filters['score_type']),
                fn ($q) => $q->where('score_type', $filters['score_type'])
            )
            ->when(
                isset($filters['is_published']) && $filters['is_published'] !== '',
                fn ($q) => $q->where(
                    'is_published',
                    filter_var($filters['is_published'], FILTER_VALIDATE_BOOLEAN)
                )
            )
            ->when(
                ! empty($filters['search']),
                fn ($q) => $q->where('title', 'like', '%' . $filters['search'] . '%')
            )
            ->when(
                ! empty($filters['date_from']),
                fn ($q) => $q->whereDate('score_date', '>=', $filters['date_from'])
            )
            ->when(
                ! empty($filters['date_to']),
                fn ($q) => $q->whereDate('score_date', '<=', $filters['date_to'])
            )
            ->orderByDesc('score_date')
            ->orderByDesc('score_session_id')
            ->paginate($perPage);
    }

    /**
     * Find a session by ID with full relations.
     */
    public function findById(int $id): ?ScoreSession
    {
        return ScoreSession::with([
            'gradeSubject.subject',
            'gradeSubject.grade',
            'gradeSubject.teacher',
            'semester',
            'scoreDetails.student',
        ])->find($id);
    }

    /**
     * Create a new score session.
     */
    public function createSession(array $data): ScoreSession
    {
        return ScoreSession::create($data);
    }

    /**
     * Update session fields.
     */
    public function updateSession(ScoreSession $session, array $data): ScoreSession
    {
        $session->update($data);
        return $session->fresh([
            'gradeSubject.subject',
            'gradeSubject.grade',
            'semester',
            'scoreDetails.student',
        ]);
    }

    /**
     * Delete a session (and cascade its details via DB constraint / manual).
     */
    public function deleteSession(ScoreSession $session): void
    {
        $session->scoreDetails()->delete();
        $session->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | DETAILS — queries
    |--------------------------------------------------------------------------
    */

    /**
     * Bulk-upsert score details for a session.
     */
    public function upsertDetails(int $sessionId, array $details): void
    {
        $rows = array_map(fn ($d) => [
            'score_session_id' => $sessionId,
            'student_id'       => $d['student_id'],
            'score'            => isset($d['score']) && $d['score'] !== '' ? $d['score'] : null,
            'max_score'        => $d['max_score'] ?? 100,
            'notes'            => $d['notes']     ?? null,
            'created_at'       => now(),
            'updated_at'       => now(),
        ], $details);

        ScoreDetail::upsert(
            $rows,
            uniqueBy: ['score_session_id', 'student_id'],
            update:   ['score', 'max_score', 'notes', 'updated_at']
        );
    }

    /**
     * Get all details for a session (with student relation).
     */
    public function getDetailsBySession(int $sessionId): Collection
    {
        return ScoreDetail::with('student')
            ->where('score_session_id', $sessionId)
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
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'nis']);
    }

    /*
    |--------------------------------------------------------------------------
    | STATS
    |--------------------------------------------------------------------------
    */

    /**
     * Count sessions by score_type for a teacher in a semester.
     */
    public function countByType(int $teacherId, ?int $semesterId = null): array
    {
        $rows = ScoreSession::whereHas(
                'gradeSubject',
                fn ($q) => $q->where('teacher_id', $teacherId)
            )
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->selectRaw('score_type, COUNT(*) as total')
            ->groupBy('score_type')
            ->pluck('total', 'score_type');

        $total = ScoreSession::whereHas(
            'gradeSubject',
            fn ($q) => $q->where('teacher_id', $teacherId)
        )
        ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
        ->count();

        $published = ScoreSession::whereHas(
            'gradeSubject',
            fn ($q) => $q->where('teacher_id', $teacherId)
        )
        ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
        ->where('is_published', true)
        ->count();

        return [
            'total'             => $total,
            'published'         => $published,
            'draft'             => $total - $published,
            'daily'             => $rows['daily']    ?? 0,
            'mid_exam'          => $rows['mid_exam']        ?? 0,
            'final_exam'        => $rows['final_exam']        ?? 0,
            'assignment'         => $rows['assignment']      ?? 0,
        ];
    }
}