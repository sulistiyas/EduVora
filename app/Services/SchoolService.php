<?php

namespace App\Services;

use App\Repositories\SchoolRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SchoolService
{
    protected $schoolRepository;

    public function __construct(SchoolRepository $schoolRepository)
    {
        $this->schoolRepository = $schoolRepository;
    }

    public function getAllSchools(array $filters = []): LengthAwarePaginator|Collection
    {
        return $this->schoolRepository->getAllSchools($filters);
    }

    public function getSchoolById($id)
    {
        return $this->schoolRepository->getSchoolById($id);
    }

    public function createSchool($data)
    {
        return $this->schoolRepository->createSchool($data);
    }

    public function updateSchool($id, $data)
    {
        return $this->schoolRepository->updateSchool($id, $data);
    }

    public function toggleStatus($id)
    {
        return $this->schoolRepository->toggleStatus($id);
    }

    public function deleteSchool($id)
    {
        return $this->schoolRepository->deleteSchool($id);
    }
}
