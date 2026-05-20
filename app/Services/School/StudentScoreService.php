<?php

namespace App\Services\School;

use App\Models\Academic\Schedule;
use App\Models\Student\ScoreSession;
use App\Repositories\School\StudentScoreRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class StudentScoreService
{
    public function __construct(
        protected StudentScoreRepository $repo
    ) {}

    /*
    |--------------------------------------------------------------------------
    | SESSION
    |--------------------------------------------------------------------------
    */

    public function paginate(int $teacherId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repo->paginateByTeacher($teacherId, $filters, $perPage);
    }

    public function findOrFail(int $id): ScoreSession
    {
        $session = $this->repo->findById($id);
        abort_if(is_null($session), 404, 'Sesi nilai tidak ditemukan.');
        return $session;
    }

    public function authorizeTeacher(ScoreSession $session, int $teacherId): void
    {
        abort_if(
            $session->gradeSubject?->teacher_id !== $teacherId,
            403,
            'Anda tidak memiliki akses ke sesi nilai ini.'
        );
    }

    /**
     * Create a new score session and auto-seed students with null score.
     */
    public function createSession(array $data, int $gradeId, float $maxScore = 100): ScoreSession
    {
        $session = $this->repo->createSession($data);

        // Auto-seed all students in the grade with null scores
        $students = $this->repo->getStudentsByGrade($gradeId);
        $details  = $students->map(fn ($s) => [
            'student_id' => $s->id,
            'score'      => null,
            'max_score'  => $maxScore,
            'notes'      => null,
        ])->toArray();

        $this->repo->upsertDetails($session->score_session_id, $details);

        return $this->repo->findById($session->score_session_id);
    }

    /**
     * Save score details (bulk upsert).
     */
    public function saveDetails(ScoreSession $session, array $details, ?array $sessionUpdate = null): ScoreSession
    {
        $this->repo->upsertDetails($session->score_session_id, $details);

        if ($sessionUpdate) {
            $session = $this->repo->updateSession($session, $sessionUpdate);
        }

        return $this->repo->findById($session->score_session_id);
    }

    /**
     * Publish / unpublish a session.
     */
    public function togglePublish(ScoreSession $session): ScoreSession
    {
        return $this->repo->updateSession($session, [
            'is_published' => ! $session->is_published,
        ]);
    }

    /**
     * Update session metadata (title, description, score_type, score_date, max_score).
     */
    public function updateSession(ScoreSession $session, array $data): ScoreSession
    {
        return $this->repo->updateSession($session, $data);
    }

    /**
     * Delete a score session and all its details.
     */
    public function deleteSession(ScoreSession $session): void
    {
        $this->repo->deleteSession($session);
    }

    /*
    |--------------------------------------------------------------------------
    | STUDENTS
    |--------------------------------------------------------------------------
    */

    public function getStudentsByGrade(int $gradeId): Collection
    {
        return $this->repo->getStudentsByGrade($gradeId);
    }

    /*
    |--------------------------------------------------------------------------
    | STATS
    |--------------------------------------------------------------------------
    */

    public function stats(int $teacherId, ?int $semesterId = null): array
    {
        return $this->repo->countByType($teacherId, $semesterId);
    }

    /*
    |--------------------------------------------------------------------------
    | RESOURCE — shape for frontend
    |--------------------------------------------------------------------------
    */

    public function toResource(ScoreSession $session): array
    {
        $details   = $session->scoreDetails;
        $scores    = $details->whereNotNull('score')->pluck('score');
        $maxScore  = $details->first()?->max_score ?? 100;
        $avg       = $scores->count() > 0 ? round($scores->avg(), 2) : null;
        $highest   = $scores->count() > 0 ? $scores->max()           : null;
        $lowest    = $scores->count() > 0 ? $scores->min()           : null;
        $filled    = $scores->count();
        $total     = $details->count();

        return [
            'score_session_id'  => $session->score_session_id,
            'grade_subject_id'  => $session->grade_subject_id,
            'semester_id'       => $session->semester_id,
            'score_type'        => $session->score_type,
            'score_type_label'  => $this->scoreTypeLabel($session->score_type),
            'title'             => $session->title,
            'description'       => $session->description,
            'score_date'        => $session->score_date?->toDateString(),
            'score_date_label'  => $session->score_date?->translatedFormat('l, d F Y'),
            'is_published'      => $session->is_published,
            'max_score'         => (float) ($maxScore ?? 100),

            // Relations
            'subject_name'      => $session->gradeSubject?->subject?->subject_name,
            'grade_name'        => $session->gradeSubject?->grade?->grade_name,
            'semester_name'     => $session->semester?->semester_name,

            // Aggregates
            'total_students'    => $total,
            'filled_count'      => $filled,
            'avg_score'         => $avg,
            'highest_score'     => $highest,
            'lowest_score'      => $lowest,
        ];
    }

    public function toDetailResource(ScoreSession $session): array
    {
        $base    = $this->toResource($session);
        $details = $session->scoreDetails->map(fn ($d) => [
            'score_detail_id'  => $d->score_detail_id,
            'student_id'       => $d->student_id,
            'student_name'     => $d->student?->full_name,
            'nis'              => $d->student?->nis,
            'photo'            => $d->student?->photo,
            'score'            => $d->score !== null ? (float) $d->score : null,
            'max_score'        => (float) ($d->max_score ?? 100),
            'notes'            => $d->notes,
            'percentage'       => $d->score !== null && ($d->max_score ?? 100) > 0
                                    ? round(($d->score / ($d->max_score ?? 100)) * 100, 1)
                                    : null,
        ]);

        return array_merge($base, ['details' => $details]);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function scoreTypeLabel(string $type): string
    {
        return [
            'daily' => 'Ulangan Harian',
            'mid_exam'     => 'UTS',
            'final_exam'     => 'UAS',
            'assignment'   => 'Tugas',
        ][$type] ?? ucfirst($type);
    }
}