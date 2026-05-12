<?php

namespace App\Repositories\School;

use App\Models\Academic\Subject;
use App\Models\Core\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TeacherRepository
{
    /**
     * Base query: hanya user dengan role 'teacher' dan sekolah tertentu.
     */
    private function baseQuery(int $schoolId)
    {
        return User::query()
            ->select([
                'id',
                'name',
                'email',
                'email_verified_at',
                'profile_picture',
                'phone_number',
                'status',
                'created_at',
                'updated_at',
            ])
            ->with([
                'roles'   => fn($q) => $q->select(['roles.role_id', 'roles.role_name']),
                'schools' => fn($q) => $q->select([
                    'school_profiles.school_id',
                    'school_profiles.school_name',
                    'school_profiles.school_type',
                    'school_profiles.status',
                ]),
                'teacher' => fn($q) => $q->select([
                    'teacher_id',
                    'user_id',
                    'nip',
                    'nik',
                    'full_name',
                    'birth_place',
                    'birth_date',
                    'gender',
                    'religion',
                    'address',
                    'phone',
                    'email',
                    'employment_status',
                    'position',
                    'grade_level',
                    'education_level',
                    'major',
                    'certification',
                    'npwp',
                    'join_date',
                    'status',
                ]),
            ])
            ->whereHas('roles',   fn($q) => $q->where('role_name', 'teacher'))
            ->whereHas('schools', fn($q) => $q->where('school_profiles.school_id', $schoolId));
    }

    public function getAll(int $schoolId, array $filters = []): LengthAwarePaginator|Collection
    {
        $query = $this->baseQuery($schoolId);

        // Search
        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhere('phone_number', 'ILIKE', "%{$search}%")
                  ->orWhereHas('teacher', fn($qs) =>
                      $qs->where('nip', 'ILIKE', "%{$search}%")
                         ->orWhere('full_name', 'ILIKE', "%{$search}%")
                  )->orWhereHas('teacher.subjects', function ($q) use ($search) {
                        $q->where('subject_name', 'ILIKE', "%{$search}%");
                    });
            });
        }

        // Filter status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter subject
        if (!empty($filters['subject'])) {
            $query->whereHas('teacher.subjects', function ($q) use ($filters) {
                $q->where('subjects.id', $filters['subject']);
            });
        }

        // Filter employee_type
        if (!empty($filters['employee_type'])) {
            $query->whereHas('teacher', fn($q) =>
                $q->where('employee_type', $filters['employee_type'])
            );
        }

        // Sorting
        $allowed   = ['name', 'email', 'created_at', 'updated_at', 'status'];
        $sortBy    = in_array($filters['sort_by'] ?? '', $allowed) ? $filters['sort_by'] : 'created_at';
        $sortOrder = in_array($filters['sort_order'] ?? '', ['asc', 'desc']) ? $filters['sort_order'] : 'asc';
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $filters['per_page'] ?? 10;
        if ($perPage === 'all') {
            return $query->get();
        }

        return $query->paginate((int) $perPage)->withQueryString();
    }

    public function findById(int $schoolId, $id): User
    {
        return $this->baseQuery($schoolId)->findOrFail($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update($id, array $data): ?User
    {
        $user = User::find($id);
        if ($user) {
            $user->update($data);
            return $user;
        }
        return null;
    }

    public function toggleStatus($id): ?User
    {
        $user = User::find($id);
        if ($user) {
            $user->update([
                'status' => $user->status === 'active' ? 'inactive' : 'active',
            ]);
            return $user;
        }
        return null;
    }

    public function delete($id): bool
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return true;
        }
        return false;
    }

    /**
     * Ambil daftar subject unik di sekolah ini (untuk filter dropdown).
     */
    public function getSubjects(int $schoolId): array
    {
        return Subject::query()
                ->where('school_id', $schoolId)
                ->orderBy('subject_name')
                ->pluck('subject_name', 'id')
                ->toArray();
    }
}