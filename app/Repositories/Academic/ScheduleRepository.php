<?php

namespace App\Repositories\Academic;

use App\Concerns\HasSchoolScope;
use App\Models\Academic\Schedule;
use Illuminate\Pagination\LengthAwarePaginator;

class ScheduleRepository
{
    use HasSchoolScope;

    /**
     * Paginate schedules with eager-loaded relations.
     */
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Schedule::with([
            'gradeSubject.grade',
            'gradeSubject.subject',
            'gradeSubject.teacher',
            'room',
            'semester',
        ])
            ->whereHas('gradeSubject.grade', function ($q) {
                $q->where('school_id', $this->getAuthSchoolId());
            })
            ->when(
                ! empty($filters['search']),
                fn ($q) => $q->whereHas(
                    'gradeSubject.subject',
                    fn ($sq) => $sq->where('subject_name', 'like', "%{$filters['search']}%")
                )->orWhereHas(
                    'gradeSubject.grade',
                    fn ($gq) => $gq->where('grade_name', 'like', "%{$filters['search']}%")
                )->orWhereHas(
                    'room',
                    fn ($rq) => $rq->where('room_name', 'like', "%{$filters['search']}%")
                )
            )
            ->when(
                ! empty($filters['status']),
                fn ($q) => $q->where('status', $filters['status'])
            )
            ->when(
                ! empty($filters['semester_id']),
                fn ($q) => $q->where('semester_id', $filters['semester_id'])
            )
            ->when(
                ! empty($filters['day_of_week']),
                fn ($q) => $q->where('day_of_week', $filters['day_of_week'])
            )
            ->when(
                ! empty($filters['session_type']),
                fn ($q) => $q->where('session_type', $filters['session_type'])
            )
            ->orderByTime()
            ->paginate($perPage);
    }

    public function paginateByTeacher(int $teacherId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Schedule::with([
            'gradeSubject.grade',
            'gradeSubject.subject',
            'gradeSubject.teacher',
            'room',
            'semester',
        ])
            ->whereHas('gradeSubject', fn ($q) => $q->where('teacher_id', $teacherId))
            ->when(
                ! empty($filters['semester_id']),
                fn ($q) => $q->where('semester_id', $filters['semester_id'])
            )
            ->when(
                ! empty($filters['day_of_week']),
                fn ($q) => $q->where('day_of_week', $filters['day_of_week'])
            )
            ->when(
                ! empty($filters['session_type']),
                fn ($q) => $q->where('session_type', $filters['session_type'])
            )
            ->where('status', Schedule::STATUS_ACTIVE)
            ->orderByTime()
            ->paginate($perPage);
    }

    /**
     * Find a schedule by ID with relations.
     */
    public function findById(int $id): ?Schedule
    {
        return Schedule::with([
            'gradeSubject.grade',
            'gradeSubject.subject',
            'gradeSubject.teacher',
            'room',
            'semester',
        ])->whereHas('gradeSubject.grade', function ($q) {
            $q->where('school_id', $this->getAuthSchoolId());
        })->find($id);
    }

    /**
     * Create a new schedule.
     */
    public function create(array $data): Schedule
    {
        return Schedule::create($data);
    }

    /**
     * Update an existing schedule.
     */
    public function update(Schedule $schedule, array $data): Schedule
    {
        $schedule->update($data);

        return $schedule->fresh([
            'gradeSubject.grade',
            'gradeSubject.subject',
            'gradeSubject.teacher',
            'room',
            'semester',
        ]);
    }

    /**
     * Delete a schedule.
     */
    public function delete(Schedule $schedule): bool
    {
        return $schedule->delete();
    }

    /**
     * Toggle the active/inactive status.
     */
    public function toggleStatus(Schedule $schedule): Schedule
    {
        $schedule->update([
            'status' => $schedule->isActive()
                ? Schedule::STATUS_INACTIVE
                : Schedule::STATUS_ACTIVE,
        ]);

        return $schedule->fresh();
    }

    /**
     * Check whether a given time slot conflicts with existing schedules,
     * optionally excluding a specific schedule ID (for updates).
     */
    public function hasConflict(
        int $roomId,
        int $dayOfWeek,
        string $startTime,
        string $endTime,
        int $semesterId,
        ?int $excludeId = null
    ): bool {
        return Schedule::where('room_id', $roomId)
            ->where('day_of_week', $dayOfWeek)
            ->where('semester_id', $semesterId)
            ->where('status', Schedule::STATUS_ACTIVE)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->when($excludeId, fn ($q) => $q->where('schedule_id', '!=', $excludeId))
            ->exists();
    }
}
