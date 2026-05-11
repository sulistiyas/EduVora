<?php

namespace App\Services\Academic;

use App\Models\Academic\AcademicYear;
use App\Models\Academic\Subject;
use App\Repositories\Academic\SubjectsRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SubjectsService
{
    protected $subjectRepository;

    public function __construct(SubjectsRepository $subjectRepository)
    {
        $this->subjectRepository = $subjectRepository;
    }

    public function getAllSubjects($filters = []): LengthAwarePaginator|Collection
    {
        return $this->subjectRepository->getAllSubjects($filters);
    }

    public function getSubjectById($id)
    {
        return $this->subjectRepository->getSubjectById($id);
    }

    public function createSubject($data)
    {
        return $this->subjectRepository->createSubject($data);
    }

    public function updateSubject($id, $data)
    {
        return $this->subjectRepository->updateSubject($id, $data);
    }

    public function toggleStatus(int $id): ?Subject
    {
        return $this->subjectRepository->toggleStatus($id);
    }

    public function deleteSubject($id)
    {
        return $this->subjectRepository->deleteSubject($id);
    }
}