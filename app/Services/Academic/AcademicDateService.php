<?php

namespace App\Services\Academic;

use App\Models\Academic\AcademicDate;
use App\Repositories\Academic\AcademicDateRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class AcademicDateService
{
    public function __construct(
        protected AcademicDateRepository $repo
    ) {}

    public function paginate(int $schoolId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repo->paginateBySchool($schoolId, $filters, $perPage);
    }

    public function findOrFail(int $id): AcademicDate
    {
        $date = $this->repo->findById($id);
        abort_if(is_null($date), 404, 'Tanggal akademik tidak ditemukan.');

        return $date;
    }

    public function create(int $schoolId, array $data): AcademicDate
    {
        return $this->repo->create(array_merge($data, [
            'school_id' => $schoolId,
        ]));
    }

    public function update(AcademicDate $date, array $data): AcademicDate
    {
        return $this->repo->update($date, $data);
    }

    public function delete(AcademicDate $date): bool
    {
        return $this->repo->delete($date);
    }

    public function toResource(AcademicDate $date): array
    {
        return [
            'academic_date_id' => $date->academic_date_id,
            'title' => $date->title,
            'description' => $date->description,
            'start_date' => $date->start_date?->toDateString(),
            'end_date' => $date->end_date?->toDateString(),
            'type' => $date->type,
            'type_label' => $date->type_label,
            'status' => $date->status,
            'duration_label' => $date->duration_label,
            'semester_name' => $date->semester?->semester_name,
            'is_upcoming' => $date->isUpcoming(),
            'is_ongoing' => $date->isOngoing(),
        ];
    }
}
