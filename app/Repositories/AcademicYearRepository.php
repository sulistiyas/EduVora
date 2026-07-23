<?php

namespace App\Repositories;

use App\Concerns\HasSchoolScope;
use App\Models\Academic\AcademicYear;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AcademicYearRepository
{
    use HasSchoolScope;

    public function getAllAcademicYears(array $filters = []): LengthAwarePaginator|Collection
    {
        $query = AcademicYear::query()
            ->select([
                'academic_year_id',
                'academic_year_name',
                'start_date',
                'end_date',
                'status',
            ])
            ->where('school_id', $this->getAuthSchoolId());

        // 🔍 Search (hanya kalau ada input)
        if (! empty($filters['search'])) {
            $search = strtolower($filters['search']);

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(academic_year_name) ILIKE ?', ["%{$search}%"]);
            });
        }

        // 🎯 Filter status
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // 🔽 Sorting (whitelist biar aman)
        $allowedSort = ['academic_year_name', 'status', 'created_at'];
        $sortBy = in_array($filters['sort_by'] ?? '', $allowedSort)
            ? $filters['sort_by']
            : 'created_at';

        $sortOrder = $filters['sort_order'] ?? 'desc';

        $query->orderBy($sortBy, $sortOrder);

        // 📄 Pagination
        $perPage = $filters['per_page'] ?? 10;

        if ($perPage === 'all') {
            return $query->get();
        }

        return $query->paginate((int) $perPage)->withQueryString();
    }

    public function getAcademicYearById($id)
    {
        return AcademicYear::where('school_id', $this->getAuthSchoolId())->find($id);
    }

    public function createAcademicYear($data)
    {
        $data['school_id'] = $this->getAuthSchoolId();

        return AcademicYear::create($data);
    }

    public function updateAcademicYear($id, $data)
    {
        $academicYear = AcademicYear::where('academic_year_id', $id)
            ->where('school_id', $this->getAuthSchoolId() ?? null)->first();
        if ($academicYear) {
            $academicYear->update($data);

            return $academicYear;
        }

        return null;
    }

    public function deactivateOthersBySchool(int $schoolId, int $exceptId): void
    {
        AcademicYear::where('school_id', $schoolId)
            ->where('academic_year_id', '!=', $exceptId)
            ->where('status', 'active')
            ->update(['status' => 'inactive']);
    }

    public function toggleStatus(int $id): ?AcademicYear
    {
        $academicYear = AcademicYear::find($id);

        if (! $academicYear) {
            return null;
        }

        if ($academicYear->status === 'inactive') {
            // Nonaktifkan semua yang lain dalam sekolah yang sama dulu
            $this->deactivateOthersBySchool($academicYear->school_id, $id);
            $academicYear->status = 'active';
        } else {
            $academicYear->status = 'inactive';
        }

        $academicYear->save();

        return $academicYear;
    }

    // public function toggleStatus($id): ?AcademicYear
    // {
    //     $academicYear = AcademicYear::find($id);
    //     if ($academicYear) {
    //         $academicYear->update([
    //             'status' => $academicYear->status === 'active' ? 'inactive' : 'active',
    //         ]);
    //         return $academicYear;
    //     }
    //     return null;
    // }

    public function deleteAcademicYear($id)
    {
        $academicYear = AcademicYear::where('academic_year_id', $id)
            ->where('school_id', $this->getAuthSchoolId() ?? null)->first();
        if ($academicYear) {
            $academicYear->delete();

            return true;
        }

        return false;
    }
}
