<?php

namespace App\Repositories;

use App\Concerns\HasSchoolScope;
use App\Models\Academic\AcademicYear;
use App\Models\Academic\Grade;
use App\Models\Academic\Room;
use App\Models\Academic\Subject;
use App\Models\Student\Student;
use App\Models\Teacher\Teacher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class GradesRepository
{
    use HasSchoolScope;

    // ─── Read ──────────────────────────────────────────────────────────────────

    public function getAllGrades(array $filters = []): LengthAwarePaginator|Collection
    {
        $schoolId = $this->getAuthSchoolId();

        $query = Grade::query()
            ->select([
                'grade_id',
                'school_id',
                'academic_year_id',
                'room_id',
                'homeroom_teacher_id',
                'grade_name',
                'level',
                'status',
                'created_at',
            ])
            ->with([
                'academicYear' => fn ($q) => $q->select([
                    'academic_year_id',
                    'academic_year_name',
                    'status',
                ]),
                'room' => fn ($q) => $q->select([
                    'room_id',
                    'room_name',
                    'code',
                    'type',
                    'floor',
                    'building',
                    'capacity',
                ]),
                'homeroomTeacher' => fn ($q) => $q->select([
                    'teacher_id',
                    'full_name',
                    'nip',
                    'status',
                ]),
            ])
            ->where('school_id', $schoolId);

        // 🔍 Search — grade_name, level
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('grade_name', 'ILIKE', "%{$search}%")
                    ->orWhereRaw('CAST(level AS TEXT) ILIKE ?', ["%{$search}%"]);
            });
        }

        // 🎯 Filter status
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // 🎯 Filter by room
        if (! empty($filters['room_id'])) {
            $query->where('room_id', $filters['room_id']);
        }

        // 🎯 Filter by academic year
        if (! empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }

        // 🔽 Sorting
        $allowedSort = ['grade_name', 'level', 'status', 'created_at'];
        $sortBy = in_array($filters['sort_by'] ?? '', $allowedSort)
            ? $filters['sort_by']
            : 'level';
        $sortOrder = $filters['sort_order'] ?? 'asc';

        $query->orderBy($sortBy, $sortOrder);

        // 📄 Pagination
        $perPage = $filters['per_page'] ?? 10;

        if ($perPage === 'all') {
            return $query->get();
        }

        return $query->paginate((int) $perPage)->withQueryString();
    }

    public function getGradeById($id): ?Grade
    {
        return Grade::with([
            'academicYear' => fn ($q) => $q->select([
                'academic_year_id',
                'academic_year_name',
                'status',
            ]),
            'room' => fn ($q) => $q->select([
                'room_id',
                'room_name',
                'code',
                'type',
                'floor',
                'building',
                'capacity',
            ]),
            'homeroomTeacher' => fn ($q) => $q->select([
                'teacher_id',
                'full_name',
                'nip',
                'status',
            ]),
            'students' => fn ($q) => $q->select([
                'id',
                'grade_id',
                'nis',
                'full_name',
                'gender',
            ]),
        ])->where('school_id', $this->getAuthSchoolId())->find($id);
    }

    // ─── Dropdown Data ─────────────────────────────────────────────────────────

    public function getRoomsForDropdown(): Collection
    {
        $schoolId = $this->getAuthSchoolId();

        return Room::where('school_id', $schoolId)
            ->select(['room_id', 'room_name', 'code', 'type', 'floor', 'building'])
            ->orderBy('room_name')
            ->get();
    }

    public function getTeachersForDropdown(): Collection
    {
        $schoolId = $this->getAuthSchoolId();

        return Teacher::whereHas('user.schools', function ($q) use ($schoolId) {
            $q->where('school_profiles.school_id', $schoolId);
        })
            ->where('status', 'active')
            ->select(['teacher_id', 'full_name', 'nip'])
            ->orderBy('full_name')
            ->get();
    }

    public function getAcademicYearsForDropdown(): Collection
    {
        $schoolId = $this->getAuthSchoolId();

        return AcademicYear::where('school_id', $schoolId)
            ->select(['academic_year_id', 'academic_year_name', 'status'])
            ->orderBy('academic_year_name', 'desc')
            ->get();
    }

    public function getSubjectsForDropdown(): Collection
    {
        $schoolId = $this->getAuthSchoolId();

        return Subject::where('school_id', $schoolId)
            ->where('status', 'active')
            ->select(['id', 'subject_name', 'subject_code'])
            ->orderBy('subject_name')
            ->get();
    }

    // ─── Write ─────────────────────────────────────────────────────────────────

    public function createGrade(array $data): Grade
    {
        $schoolId = $this->getAuthSchoolId();

        return Grade::create(array_merge($data, ['school_id' => $schoolId]));
    }

    public function updateGrade($id, array $data): ?Grade
    {
        $grade = Grade::where('school_id', $this->getAuthSchoolId())->find($id);

        if (! $grade) {
            return null;
        }

        $grade->update($data);

        return $grade->fresh(['academicYear', 'room', 'homeroomTeacher']);
    }

    // ─── Toggle Status ─────────────────────────────────────────────────────────

    public function toggleStatus($id): ?Grade
    {
        $grade = Grade::find($id);

        if (! $grade) {
            return null;
        }

        // Ownership check
        if ($grade->school_id !== $this->getAuthSchoolId()) {
            throw new \Exception('Akses ditolak.');
        }

        $transitions = [
            'active' => 'inactive',
            'inactive' => 'active',
            'graduated' => 'archived',
            'archived' => 'inactive',
        ];

        $grade->status = $transitions[$grade->status] ?? 'inactive';
        $grade->save();

        return $grade;
    }

    // ─── Delete ────────────────────────────────────────────────────────────────

    public function deleteGrade($id): bool
    {
        $grade = Grade::where('school_id', $this->getAuthSchoolId())->find($id);

        if (! $grade) {
            return false;
        }

        $grade->delete();

        return true;
    }

    /**
     * Ambil daftar siswa untuk assign kelas
     */
    public function getStudentsForAssign(
        ?int $gradeId = null,
        ?string $search = null
    ): Collection {
        $schoolId = $this->getAuthSchoolId();

        $query = Student::query()
            ->select([
                'id',
                'user_id',
                'nis',
                'full_name',
                'gender',
                'grade_id',
                'status',
            ])
            ->where('status', 'active')
            ->whereHas('user.schools', function ($q) use ($schoolId) {
                $q->where('school_profiles.school_id', $schoolId);
            });

        // Filter grade tertentu
        if (! is_null($gradeId)) {
            $query->where('grade_id', $gradeId);
        }

        // Search siswa
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'ILIKE', "%{$search}%")
                    ->orWhere('nis', 'ILIKE', "%{$search}%");
            });
        }

        return $query
            ->orderBy('full_name')
            ->get();
    }

    /**
     * Assign banyak siswa ke grade
     */
    public function assignStudents(
        int $gradeId,
        array $studentIds
    ): bool {
        $schoolId = $this->getAuthSchoolId();

        $grade = Grade::where('school_id', $schoolId)
            ->where('grade_id', $gradeId)
            ->first();

        if (! $grade) {
            return false;
        }

        DB::transaction(function () use (
            $studentIds,
            $grade,
            $schoolId
        ) {

            Student::whereIn('id', $studentIds)
                ->whereHas('user.schools', function ($q) use ($schoolId) {
                    $q->where('school_profiles.school_id', $schoolId);
                })
                ->update([
                    'grade_id' => $grade->grade_id,
                    'updated_at' => now(),
                ]);
        });

        return true;
    }

    /**
     * Remove 1 siswa dari grade
     */
    public function removeStudentFromGrade(
        int $gradeId,
        int $studentId
    ): bool {
        $schoolId = $this->getAuthSchoolId();

        $grade = Grade::where('school_id', $schoolId)
            ->where('grade_id', $gradeId)
            ->first();

        if (! $grade) {
            return false;
        }

        $student = Student::where('id', $studentId)
            ->where('grade_id', $grade->grade_id)
            ->whereHas('user.schools', function ($q) use ($schoolId) {
                $q->where('school_profiles.school_id', $schoolId);
            })
            ->first();

        if (! $student) {
            return false;
        }

        $student->grade_id = null;
        $student->save();

        return true;
    }

    /**
     * Kosongkan semua siswa dari grade
     */
    public function clearStudentsFromGrade(int $gradeId): bool
    {
        $schoolId = $this->getAuthSchoolId();

        $grade = Grade::where('school_id', $schoolId)
            ->where('grade_id', $gradeId)
            ->first();

        if (! $grade) {
            return false;
        }

        Student::where('grade_id', $grade->grade_id)
            ->whereHas('user.schools', function ($q) use ($schoolId) {
                $q->where('school_profiles.school_id', $schoolId);
            })
            ->update([
                'grade_id' => null,
                'updated_at' => now(),
            ]);

        return true;
    }
}
