<?php

namespace App\Repositories;
use App\Models\Core\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
class UserRepository
{
    public function getAllUsers(array $filters = []): LengthAwarePaginator|Collection
    {
        $query = User::query()
        ->select([
            'id',
            'name',
            'email',
            'email_verified_at',
            'profile_picture',
            'phone_number',
            'status',
            'created_at',
            'updated_at'
        ])
        ->with([
            // Roles
            'roles' => function ($q) {
                $q->select([
                    'roles.role_id',
                    'roles.role_name',
                    'roles.role_description'
                ]);
            },

            // Schools
            'schools' => function ($q) {
                $q->select([
                    'school_profiles.school_id',
                    'school_profiles.school_name',
                    'school_profiles.school_type',
                    'school_profiles.status'
                ]);
            },

            // Student
            'student' => function ($q) {
                $q->select([
                    'id',
                    'user_id',
                    'nis',
                    'full_name',
                    'nick_name',
                    'email',
                    'birth_date',
                    'gender',
                    'phone_number',
                    'address',
                    'city',
                    'province',
                    'postal_code',
                    'profile_photo',
                    'grade_id',
                    'class_group',
                    'status',
                    'enrollment_date',
                    'graduation_date'
                ]);
            },

            // Teacher
            'teacher' => function ($q) {
                $q->select([
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
                    'status'
                ]);
            }
        ]);

        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%")
                    ->orWhere('phone_number', 'ILIKE', "%{$search}%")
                    ->orWhere('status', 'ILIKE', "%{$search}%")

                    // 🔹 Relasi Roles (user_has_roles)
                    ->orWhereHas('roles', function ($qr) use ($search) {
                        $qr->where('role_name', 'ILIKE', "%{$search}%");
                    })

                    // 🔹 Relasi Schools (user_has_schools)
                    ->orWhereHas('schools', function ($qs) use ($search) {
                        $qs->where('school_name', 'ILIKE', "%{$search}%")
                            ->orWhere('school_type', 'ILIKE', "%{$search}%");
                    });
            });
        }

        // filter status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('roles.role_id', $filters['role']);
            });
        }

        // Sorting
        $allowedSort = ['name', 'email', 'phone_number', 'created_at', 'updated_at','status'];
        $sortBy = in_array($filters['sort_by'] ?? '', $allowedSort)
            ? $filters['sort_by']
            : 'created_at';
        $sortOrder = in_array($filters['sort_order'] ?? '', ['asc', 'desc'])
            ? $filters['sort_order']
            : 'asc';

        $query->orderBy($sortBy, $sortOrder);
        
        // Pagination
        $perPage = $filters['per_page'] ?? 10;
        if ($perPage === 'all') {
            return $query->get();
        }
        return $query->paginate((int) $perPage)->withQueryString();
    }
    
    public function getUserById($id)
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
                'updated_at'
            ])
            ->with([
                // Roles
                'roles' => function ($q) {
                    $q->select([
                        'roles.role_id',
                        'roles.role_name',
                        'roles.role_description',
                    ]);
                },

                // Schools
                'schools' => function ($q) {
                    $q->select([
                        'school_profiles.school_id',
                        'school_profiles.school_name',
                        'school_profiles.school_type',
                        'school_profiles.status',
                    ]);
                },

                // Student profile (jika user adalah siswa)
                'student' => function ($q) {
                    $q->select([
                        'id',
                        'user_id',
                        'nis',
                        'full_name',
                        'nick_name',
                        'email',
                        'birth_date',
                        'gender',
                        'phone_number',
                        'address',
                        'city',
                        'province',
                        'postal_code',
                        'profile_photo',
                        'grade_id',
                        'class_group',
                        'status',
                        'enrollment_date',
                        'graduation_date',
                    ]);
                },

                // Teacher profile (jika user adalah guru)
                'teacher' => function ($q) {
                    $q->select([
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
                    ]);
                },
            ])
            ->findOrFail($id);
    }
    

    public function createUser($data)
    {
        return User::create($data);
    }

    public function updateUser($id, $data)
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
        $users = User::find($id);
        if ($users) {
            $users->update([
                'status' => $users->status === 'active' ? 'inactive' : 'active',
            ]);
            return $users;
        }
        return null;
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return true;
        }
        return false;
    }
}