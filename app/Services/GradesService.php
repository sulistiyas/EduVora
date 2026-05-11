<?php

namespace App\Services;

use App\Models\Academic\Grade;
use App\Repositories\GradesRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class GradesService
{
    private GradesRepository $gradeRepository;

    public function __construct(GradesRepository $gradeRepository)
    {
        $this->gradeRepository = $gradeRepository;
    }

    public function getAllGrades(array $filters = []): LengthAwarePaginator|Collection
    {
        return $this->gradeRepository->getAllGrades($filters);
    }

    public function getSubjectsForDropdown(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->gradeRepository->getSubjectsForDropdown();
    }

    public function getGradeById($id): ?Grade
    {
        return $this->gradeRepository->getGradeById($id);
    }

    public function getRoomsForDropdown(): Collection
    {
        return $this->gradeRepository->getRoomsForDropdown();
    }

    public function getTeachersForDropdown(): Collection
    {
        return $this->gradeRepository->getTeachersForDropdown();
    }

    public function getAcademicYearsForDropdown(): Collection
    {
        return $this->gradeRepository->getAcademicYearsForDropdown();
    }

    public function createGrade(array $data): ?Grade
    {
        return $this->gradeRepository->createGrade($data);
    }

    public function updateGrade($id, array $data): ?Grade
    {
        return $this->gradeRepository->updateGrade($id, $data);
    }

    public function toggleStatus($id): ?Grade
    {
        return $this->gradeRepository->toggleStatus($id);
    }

    public function deleteGrade($id): bool
    {
        return $this->gradeRepository->deleteGrade($id);
    }
}