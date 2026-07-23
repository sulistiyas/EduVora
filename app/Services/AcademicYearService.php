<?php

namespace App\Services;

use App\Models\Academic\AcademicYear;
use App\Repositories\AcademicYearRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AcademicYearService
{
    protected $academicYearRepository;

    public function __construct(AcademicYearRepository $academicYearRepository)
    {
        $this->academicYearRepository = $academicYearRepository;
    }

    public function getAllAcademicYears($filters = []): LengthAwarePaginator|Collection
    {
        return $this->academicYearRepository->getAllAcademicYears($filters);
    }

    public function getAcademicYearById($id)
    {
        return $this->academicYearRepository->getAcademicYearById($id);
    }

    public function createAcademicYear($data)
    {
        return $this->academicYearRepository->createAcademicYear($data);
    }

    public function updateAcademicYear($id, $data)
    {
        return $this->academicYearRepository->updateAcademicYear($id, $data);
    }

    public function toggleStatus(int $id): ?AcademicYear
    {
        return $this->academicYearRepository->toggleStatus($id);
    }

    public function deleteAcademicYear($id)
    {
        return $this->academicYearRepository->deleteAcademicYear($id);
    }
}
