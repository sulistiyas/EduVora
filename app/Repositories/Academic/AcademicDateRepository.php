<?php

namespace App\Repositories\Academic;

use App\Models\Academic\AcademicDate;
use Illuminate\Pagination\LengthAwarePaginator;

class AcademicDateRepository
{
    public function paginateBySchool(
        int $schoolId,
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        return AcademicDate::where('school_id', $schoolId)
            ->when(
                ! empty($filters['semester_id']),
                fn ($q) => $q->where('semester_id', $filters['semester_id'])
            )
            ->when(
                ! empty($filters['type']),
                fn ($q) => $q->where('type', $filters['type'])
            )
            ->when(
                ! empty($filters['status']),
                fn ($q) => $q->where('status', $filters['status'])
            )
            ->orderByDesc('start_date')
            ->paginate($perPage);
    }

    public function findById(int $id): ?AcademicDate
    {
        return AcademicDate::with(['semester'])->find($id);
    }

    public function create(array $data): AcademicDate
    {
        return AcademicDate::create($data);
    }

    public function update(AcademicDate $date, array $data): AcademicDate
    {
        $date->update($data);

        return $date->fresh();
    }

    public function delete(AcademicDate $date): bool
    {
        return $date->delete();
    }
}
