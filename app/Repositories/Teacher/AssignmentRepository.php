<?php

namespace App\Repositories\Teacher;

use App\Concerns\HasSchoolScope;
use App\Models\Exam\Assigment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AssignmentRepository
{
    use HasSchoolScope;

    /**
     * Get paginated assignments for a teacher with filters.
     */
    public function getTeacherAssignments(int $teacherId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Assigment::query()
            ->forSchool($this->getAuthSchoolId())
            ->where('teacher_id', $teacherId)
            ->with(['subject', 'grade'])
            ->withCount([
                'submissions as total_submissions' => function ($q) {
                    $q->where('status', '!=', 'pending');
                },
                'submissions as graded_count' => function ($q) {
                    $q->where('status', 'graded');
                },
            ]);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $query->where('title', 'ILIKE', '%'.$filters['search'].'%');
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('assigned_date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('due_date', '<=', $filters['date_to']);
        }

        return $query->latest('assigned_date')->paginate($perPage);
    }

    /**
     * Get a single assignment by ID and verify teacher ownership.
     */
    public function getAssignmentByIdAndTeacher(int $id, int $teacherId): ?Assigment
    {
        return Assigment::query()
            ->forSchool($this->getAuthSchoolId())
            ->where('id', $id)
            ->where('teacher_id', $teacherId)
            ->with([
                'subject',
                'grade',
                'submissions.student.user',
            ])
            ->first();
    }

    /**
     * Create a new assignment.
     */
    public function create(array $data): Assigment
    {
        $data['school_id'] = $this->getAuthSchoolId();

        return Assigment::create($data);
    }

    /**
     * Update an existing assignment.
     */
    public function update(Assigment $assignment, array $data): bool
    {
        return $assignment->update($data);
    }

    /**
     * Delete an assignment.
     */
    public function delete(Assigment $assignment): ?bool
    {
        return $assignment->delete();
    }

    /**
     * Get assignments counts by status for a teacher.
     */
    public function getTeacherAssignmentStats(int $teacherId): array
    {
        $query = Assigment::query()
            ->forSchool($this->getAuthSchoolId())
            ->where('teacher_id', $teacherId);

        $clonedQuery = clone $query;
        $publishedCount = (clone $clonedQuery)->where('status', 'published')->count();
        $draftCount = (clone $clonedQuery)->where('status', 'draft')->count();
        $closedCount = (clone $clonedQuery)->where('status', 'closed')->count();

        return [
            'total' => $publishedCount + $draftCount + $closedCount,
            'published' => $publishedCount,
            'draft' => $draftCount,
            'closed' => $closedCount,
        ];
    }
}
