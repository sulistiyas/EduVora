<?php

namespace App\Repositories;

use App\Concerns\HasSchoolScope;
use App\Models\Academic\AcademicYear;
use App\Models\Academic\Semester;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SemesterRepository
{
    use HasSchoolScope;

    // ─── Read ──────────────────────────────────────────────────────────────────

    public function getAllSemesters(array $filters = []): LengthAwarePaginator|Collection
    {
        $schoolId = $this->getAuthSchoolId();

        $query = Semester::query()
            ->select([
                'semester_id',
                'academic_year_id',
                'semester_name',
                'start_date',
                'end_date',
                'midterm_start_date',
                'midterm_end_date',
                'final_start_date',
                'final_end_date',
                'status',
            ])
            ->with([
                'academicYear' => function ($q) {
                    $q->select([
                        'academic_year_id',
                        'academic_year_name',
                        'start_date',
                        'end_date',
                        'status',
                    ]);
                },
            ])
            // Filter hanya semester milik sekolah yang login
            ->whereHas('academicYear', function ($q) use ($schoolId, $filters) {
                $q->where('school_id', $schoolId);

                if (! empty($filters['academic_year_status'])) {
                    $q->where('status', $filters['academic_year_status']);
                }
            });

        // 🔍 Search
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('semester_name', 'ILIKE', "%{$search}%")
                    ->orWhereHas('academicYear', function ($q2) use ($search) {
                        $q2->where('academic_year_name', 'ILIKE', "%{$search}%");
                    });
            });
        }

        // 🎯 Filter status
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // 🎯 Filter per academic year
        if (! empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }

        // 🔽 Sorting
        $allowedSort = ['semester_name', 'status', 'start_date', 'created_at'];
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

    public function getActiveAcademicSemester(array $filters = []): LengthAwarePaginator|Collection
    {
        return $this->getAllSemesters(
            array_merge($filters, [
                'academic_year_status' => 'active',
            ])
        );
    }

    public function getSemesterById($id): ?Semester
    {
        return Semester::with([
            'academicYear' => function ($q) {
                $q->select([
                    'academic_year_id',
                    'academic_year_name',
                    'start_date',
                    'end_date',
                    'status',
                ]);
            },
        ])->whereHas('academicYear', function ($q) {
            $q->where('school_id', $this->getAuthSchoolId());
        })->find($id);
    }

    // ─── Write ─────────────────────────────────────────────────────────────────

    public function createSemester(array $data): Semester
    {
        $academicYear = AcademicYear::where('academic_year_id', $data['academic_year_id'])
            ->where('school_id', $this->getAuthSchoolId())
            ->first();

        if (! $academicYear) {
            throw new \Exception('Tahun ajaran tidak ditemukan atau bukan milik sekolah ini.');
        }

        return Semester::create($data);
    }

    public function updateSemester($id, array $data): ?Semester
    {
        $semester = Semester::whereHas('academicYear', function ($q) {
            $q->where('school_id', $this->getAuthSchoolId());
        })->find($id);

        if (! $semester) {
            return null;
        }

        $semester->update($data);

        return $semester;
    }

    // ─── Toggle Status ─────────────────────────────────────────────────────────

    /**
     * Nonaktifkan semua semester lain dalam academic_year yang sama,
     * lalu aktifkan semester yang diminta.
     * Aturan: hanya 1 semester aktif per academic_year.
     */
    private function deactivateOthersByAcademicYear(int $academicYearId, int $exceptId): void
    {
        Semester::where('academic_year_id', $academicYearId)
            ->where('semester_id', '!=', $exceptId)
            ->where('status', 'active')
            ->update(['status' => 'inactive']);
    }

    public function toggleStatus($id): ?Semester
    {
        $semester = Semester::with('academicYear')->find($id);

        if (! $semester) {
            return null;
        }

        // Ownership check — pastikan semester ini milik sekolah yang login
        if ($semester->academicYear->school_id !== $this->getAuthSchoolId()) {
            throw new \Exception('Akses ditolak.');
        }

        if ($semester->status === 'inactive') {
            $this->deactivateOthersByAcademicYear($semester->academic_year_id, $id);
            $semester->status = 'active';
        } else {
            $semester->status = 'inactive';
        }

        $semester->save();

        return $semester;
    }

    // ─── Delete ────────────────────────────────────────────────────────────────

    public function deleteSemester($id): bool
    {
        $semester = Semester::whereHas('academicYear', function ($q) {
            $q->where('school_id', $this->getAuthSchoolId());
        })->find($id);

        if (! $semester) {
            return false;
        }

        $semester->delete();

        return true;
    }
}
