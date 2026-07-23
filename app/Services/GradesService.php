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

    // ───────────────────────────────────────────────────────────────────────────
    // READ
    // ───────────────────────────────────────────────────────────────────────────

    public function getAllGrades(array $filters = []): LengthAwarePaginator|Collection
    {
        return $this->gradeRepository->getAllGrades($filters);
    }

    public function getGradeById($id): ?Grade
    {
        return $this->gradeRepository->getGradeById($id);
    }

    // ───────────────────────────────────────────────────────────────────────────
    // DROPDOWN
    // ───────────────────────────────────────────────────────────────────────────

    public function getSubjectsForDropdown(): Collection
    {
        return $this->gradeRepository->getSubjectsForDropdown();
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

    // ───────────────────────────────────────────────────────────────────────────
    // STUDENT ASSIGNMENT
    // ───────────────────────────────────────────────────────────────────────────

    public function getStudentsForAssign(
        ?int $gradeId = null,
        ?string $search = null
    ): Collection {
        return $this->gradeRepository->getStudentsForAssign(
            $gradeId,
            $search
        );
    }

    public function assignStudents(
        int $gradeId,
        array $studentIds
    ): bool {
        return $this->gradeRepository->assignStudents(
            $gradeId,
            $studentIds
        );
    }

    public function removeStudentFromGrade(
        int $gradeId,
        int $studentId
    ): bool {
        return $this->gradeRepository->removeStudentFromGrade(
            $gradeId,
            $studentId
        );
    }

    public function clearStudentsFromGrade(int $gradeId): bool
    {
        return $this->gradeRepository->clearStudentsFromGrade($gradeId);
    }

    // ───────────────────────────────────────────────────────────────────────────
    // WRITE
    // ───────────────────────────────────────────────────────────────────────────

    public function createGrade(array $data): ?Grade
    {
        return $this->gradeRepository->createGrade($data);
    }

    public function updateGrade($id, array $data): ?Grade
    {
        return $this->gradeRepository->updateGrade($id, $data);
    }

    // ───────────────────────────────────────────────────────────────────────────
    // STATUS
    // ───────────────────────────────────────────────────────────────────────────

    public function toggleStatus($id): ?Grade
    {
        return $this->gradeRepository->toggleStatus($id);
    }

    // ───────────────────────────────────────────────────────────────────────────
    // DELETE
    // ───────────────────────────────────────────────────────────────────────────

    public function deleteGrade($id): bool
    {
        return $this->gradeRepository->deleteGrade($id);
    }
}
