<?php

namespace App\Services;

use App\Models\Academic\Semester;
use App\Repositories\SemesterRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SemesterService
{
    private $semesterRepository;

    public function __construct(SemesterRepository $semesterRepository)
    {
        $this->semesterRepository = $semesterRepository;
    }

    public function getAllSemesters(array $filters = []): LengthAwarePaginator|Collection
    {
        return $this->semesterRepository->getAllSemesters($filters);
    }

    public function getSemesterById($id): ?Semester
    {
        return $this->semesterRepository->getSemesterById($id);
    }

    public function createSemester($data): ?Semester
    {
        return $this->semesterRepository->createSemester($data);
    }

    public function updateSemester($id, $data): ?Semester
    {
        return $this->semesterRepository->updateSemester($id, $data);
    }

    public function toggleStatus($id): ?Semester
    {
        return $this->semesterRepository->toggleStatus($id);
    }

    public function deleteSemester($id): bool
    {
        return $this->semesterRepository->deleteSemester($id);
    }
}