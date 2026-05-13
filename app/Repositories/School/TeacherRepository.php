<?php

namespace App\Repositories\School;

use App\Models\Core\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TeacherRepository
{
    /**
     * Base query: hanya user dengan role 'teacher' dan sekolah tertentu.
     * Pivot schools menggunakan tabel user_has_schools (school_id).
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
                    'created_at',
                    'updated_at',
                ]),
            ])
            ->whereHas('roles',   fn($q) => $q->where('role_name', 'teacher'))
            ->whereHas('schools', fn($q) => $q->where('school_profiles.school_id', $schoolId));
    }

    // ─────────────────────────────────────────────────────────────
    //  GET ALL
    // ─────────────────────────────────────────────────────────────
    public function getAll(int $schoolId, array $filters = []): LengthAwarePaginator|Collection
    {
        $query = $this->baseQuery($schoolId);

        // Search: nama, email, NIP, NIK, nama lengkap, jabatan
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhere('phone_number', 'ILIKE', "%{$search}%")
                  ->orWhereHas('teacher', fn($qs) =>
                      $qs->where('nip', 'ILIKE', "%{$search}%")
                         ->orWhere('nik', 'ILIKE', "%{$search}%")
                         ->orWhere('full_name', 'ILIKE', "%{$search}%")
                         ->orWhere('position', 'ILIKE', "%{$search}%")
                  );
            });
        }

        // Filter status akun
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter employment_status (PNS, Honorer, dll)
        if (!empty($filters['employment_status'])) {
            $query->whereHas('teacher', fn($q) =>
                $q->where('employment_status', $filters['employment_status'])
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
     * Ambil daftar employment_status unik untuk filter dropdown.
     */
    public function getEmploymentStatuses(int $schoolId): array
    {
        return User::whereHas('roles',   fn($q) => $q->where('role_name', 'teacher'))
            ->whereHas('schools', fn($q) => $q->where('school_profiles.school_id', $schoolId))
            ->whereHas('teacher', fn($q) => $q->whereNotNull('employment_status'))
            ->with(['teacher:user_id,employment_status'])
            ->get()
            ->pluck('teacher.employment_status')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }
}