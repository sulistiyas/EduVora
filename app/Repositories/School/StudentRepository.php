<?php

namespace App\Repositories\School;

use App\Models\Core\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class StudentRepository
{
    /**
     * Base query: hanya user dengan role 'student' dan sekolah tertentu.
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
                'roles' => fn ($q) => $q->select(['roles.role_id', 'roles.role_name']),
                'schools' => fn ($q) => $q->select([
                    'school_profiles.school_id',
                    'school_profiles.school_name',
                    'school_profiles.school_type',
                    'school_profiles.status',
                ]),
                'student' => fn ($q) => $q->select([
                    'id', 'user_id', 'nis', 'full_name', 'nick_name', 'email',
                    'birth_date', 'gender', 'phone_number', 'address',
                    'city', 'province', 'postal_code', 'profile_photo',
                    'grade_id', 'class_group', 'status',
                    'enrollment_date', 'graduation_date',
                ]),
            ])
            ->whereHas('roles', fn ($q) => $q->where('role_name', 'student'))
            ->whereHas('schools', fn ($q) => $q->where('school_profiles.school_id', $schoolId));
    }

    public function getAll(int $schoolId, array $filters = []): LengthAwarePaginator|Collection
    {
        $query = $this->baseQuery($schoolId);

        // Search
        if (! empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%")
                    ->orWhere('phone_number', 'ILIKE', "%{$search}%")
                    ->orWhereHas('student', fn ($qs) => $qs->where('nis', 'ILIKE', "%{$search}%")
                        ->orWhere('full_name', 'ILIKE', "%{$search}%")
                        ->orWhere('class_group', 'ILIKE', "%{$search}%")
                    );
            });
        }

        // Filter status
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter class_group
        if (! empty($filters['class_group'])) {
            $query->whereHas('student', fn ($q) => $q->where('class_group', $filters['class_group'])
            );
        }

        // Sorting
        $allowed = ['name', 'email', 'created_at', 'updated_at', 'status'];
        $sortBy = in_array($filters['sort_by'] ?? '', $allowed) ? $filters['sort_by'] : 'created_at';
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
     * Ambil daftar class_group unik di sekolah ini (untuk filter dropdown).
     */
    public function getClassGroups(int $schoolId): array
    {
        return User::whereHas('roles', fn ($q) => $q->where('role_name', 'student'))
            ->whereHas('schools', fn ($q) => $q->where('school_profiles.school_id', $schoolId))
            ->whereHas('student', fn ($q) => $q->whereNotNull('class_group'))
            ->with(['student:user_id,class_group'])
            ->get()
            ->pluck('student.class_group')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }
}
