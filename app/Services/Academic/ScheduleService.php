<?php

namespace App\Services\Academic;

use App\Models\Academic\Schedule;
use App\Repositories\Academic\ScheduleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class ScheduleService
{
    public function __construct(
        protected ScheduleRepository $repo
    ) {}

    /**
     * Paginate schedules for the index view.
     */
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->repo->paginate($filters, $perPage);
    }

    /**
     * Get a single schedule or throw 404.
     */
    public function findOrFail(int $id): Schedule
    {
        $schedule = $this->repo->findById($id);

        abort_if(is_null($schedule), 404, 'Schedule tidak ditemukan.');

        return $schedule;
    }

    /**
     * Create a new schedule after validating for room conflicts.
     */
    public function create(array $data): Schedule
    {
        $this->checkConflict(
            roomId: $data['room_id'],
            dayOfWeek: $data['day_of_week'],
            startTime: $data['start_time'],
            endTime: $data['end_time'],
            semesterId: $data['semester_id'],
        );

        return $this->repo->create($data);
    }

    /**
     * Update an existing schedule after validating for room conflicts.
     */
    public function update(Schedule $schedule, array $data): Schedule
    {
        $this->checkConflict(
            roomId: $data['room_id'],
            dayOfWeek: $data['day_of_week'],
            startTime: $data['start_time'],
            endTime: $data['end_time'],
            semesterId: $data['semester_id'],
            excludeId: $schedule->schedule_id,
        );

        return $this->repo->update($schedule, $data);
    }

    /**
     * Delete a schedule.
     */
    public function delete(Schedule $schedule): void
    {
        $this->repo->delete($schedule);
    }

    /**
     * Toggle active / inactive status.
     */
    public function toggleStatus(Schedule $schedule): Schedule
    {
        return $this->repo->toggleStatus($schedule);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Throw a ValidationException if the room is already occupied at the
     * requested time on the same semester day.
     */
    protected function checkConflict(
        int $roomId,
        int $dayOfWeek,
        string $startTime,
        string $endTime,
        int $semesterId,
        ?int $excludeId = null
    ): void {
        if ($this->repo->hasConflict($roomId, $dayOfWeek, $startTime, $endTime, $semesterId, $excludeId)) {
            throw ValidationException::withMessages([
                'room_id' => 'Ruangan sudah digunakan pada waktu tersebut di hari yang sama.',
            ]);
        }
    }

    /**
     * Build a structured resource array from a Schedule model,
     * matching the shape the frontend JS expects.
     */
    public function toResource(Schedule $schedule): array
    {
        return [
            'schedule_id' => $schedule->schedule_id,
            'grade_subject_id' => $schedule->grade_subject_id,
            'room_id' => $schedule->room_id,
            'semester_id' => $schedule->semester_id,
            'day_of_week' => $schedule->day_of_week,
            'day_name' => $schedule->day_name,
            'start_time' => substr($schedule->start_time, 0, 5),
            'end_time' => substr($schedule->end_time, 0, 5),
            'time_range' => $schedule->time_range,
            'session_type' => $schedule->session_type,
            'status' => $schedule->status,

            // Nested relations — null-safe
            'subject_name' => $schedule->subject?->subject_name,
            'grade_name' => $schedule->grade?->grade_name,
            'teacher_name' => $schedule->teacher?->full_name,
            'room_name' => $schedule->room?->room_name,
            'room_code' => $schedule->room?->code,
            'semester_name' => $schedule->semester?->semester_name,
        ];
    }
}
