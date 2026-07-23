<?php

namespace App\Services;

use App\Models\Core\Role;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers(array $filters = []): LengthAwarePaginator|Collection
    {
        return $this->userRepository->getAllUsers($filters);
    }

    public function getUserById($id): array
    {
        $user = $this->userRepository->getUserById($id);

        // ✅ Deteksi user_type dari role_name (bukan dari relasi teacher/student)
        $roleNames = $user->roles->pluck('role_name')->toArray();

        $userType = match (true) {
            in_array('super-admin', $roleNames) => 'super-admin',
            in_array('headmaster', $roleNames) => 'headmaster',
            in_array('school-admin', $roleNames) => 'school-admin',
            in_array('teacher', $roleNames) => 'teacher',
            in_array('student', $roleNames) => 'student',
            in_array('student-parent', $roleNames) => 'student-parent',
            default => 'unknown',
        };

        // ✅ Profile dinamis berdasarkan userType
        $profile = match (true) {
            in_array($userType, ['teacher', 'headmaster']) && $user->teacher !== null => [
                'teacher_id' => $user->teacher->teacher_id,
                'nip' => $user->teacher->nip,
                'nik' => $user->teacher->nik,
                'full_name' => $user->teacher->full_name,
                'birth_place' => $user->teacher->birth_place,
                'birth_date' => $user->teacher->birth_date,
                'gender' => $user->teacher->gender,
                'religion' => $user->teacher->religion,
                'address' => $user->teacher->address,
                'phone' => $user->teacher->phone,
                'email' => $user->teacher->email,
                'employment_status' => $user->teacher->employment_status,
                'position' => $user->teacher->position,
                'grade_level' => $user->teacher->grade_level,
                'education_level' => $user->teacher->education_level,
                'major' => $user->teacher->major,
                'certification' => $user->teacher->certification,
                'npwp' => $user->teacher->npwp,
                'join_date' => $user->teacher->join_date,
                'status' => $user->teacher->status,
            ],
            $userType === 'student' && $user->student !== null => [
                'student_id' => $user->student->id,
                'nis' => $user->student->nis,
                'full_name' => $user->student->full_name,
                'nick_name' => $user->student->nick_name,
                'email' => $user->student->email,
                'birth_date' => $user->student->birth_date,
                'gender' => $user->student->gender,
                'phone_number' => $user->student->phone_number,
                'address' => $user->student->address,
                'city' => $user->student->city,
                'province' => $user->student->province,
                'postal_code' => $user->student->postal_code,
                'profile_photo' => $user->student->profile_photo,
                'grade_id' => $user->student->grade_id,
                'class_group' => $user->student->class_group,
                'status' => $user->student->status,
                'enrollment_date' => $user->student->enrollment_date,
                'graduation_date' => $user->student->graduation_date,
            ],
            default => null,
        };

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'is_verified' => ! is_null($user->email_verified_at),
            'profile_picture' => $user->profile_picture,
            'phone_number' => $user->phone_number,
            'status' => $user->status,
            'user_type' => $userType,  // ← dari role_name
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,

            'roles' => $user->roles->map(fn ($r) => [
                'role_id' => $r->role_id,
                'role_name' => $r->role_name,
                'role_description' => $r->role_description ?? null,
            ]),

            'schools' => $user->schools->map(fn ($s) => [
                'school_id' => $s->school_id,
                'school_name' => $s->school_name,
                'school_type' => $s->school_type,
                'status' => $s->status,
            ]),

            'profile' => $profile,
        ];
    }

    public function createUser(array $data): array
    {
        // ① Simpan data sebelum dihapus dari array
        $roleId = $data['role'] ?? null;
        $schoolId = $data['school'] ?? null;
        $profileData = $data['profile'] ?? null;
        $roleName = null;

        // ② Resolve role_name untuk menentukan tipe profil
        if ($roleId) {
            $role = Role::find($roleId);
            $roleName = $role?->role_name;
        }

        // ③ Bersihkan field non-kolom dari payload user
        unset($data['role'],$data['school'], $data['profile'], $data['password_confirmation']);
        $data['password'] = Hash::make($data['password']);

        // ④ Buat user
        $user = $this->userRepository->createUser($data);

        // ⑤ Attach role
        if ($roleId) {
            $user->roles()->attach($roleId);
        }
        // Attach School
        if ($schoolId) {
            $user->schools()->sync([$schoolId]);
        }

        // ⑥ Buat profil dinamis
        if ($profileData && $roleName) {
            $profileData['user_id'] = $user->id;

            if (in_array($roleName, ['teacher', 'headmaster'])) {
                $user->teacher()->create($profileData);
            } elseif ($roleName === 'student') {
                $user->student()->create($profileData);
            }
        }

        return $this->getUserById($user->id);
    }

    public function updateUser($id, $data): array
    {
        $roleId = $data['role'] ?? null;
        $schoolId = $data['school'] ?? null;
        $profileData = $data['profile'] ?? null;

        // Bersihkan field non-kolom
        unset($data['role'], $data['school'], $data['profile'], $data['password_confirmation']);

        // Hash password jika diisi
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user = $this->userRepository->updateUser($id, $data);
        if (! $user) {
            throw new \Exception('User tidak ditemukan.');
        }

        // Sync role
        if ($roleId !== null) {
            $user->roles()->sync([$roleId]);
        }

        // Sync school
        if ($schoolId !== null) {
            $user->schools()->sync([$schoolId]);
        }

        // Update profile
        if ($profileData) {
            // Deteksi ulang user_type dari role baru
            $role = $user->roles()->first();
            $roleName = $role?->role_name;

            if (in_array($roleName, ['teacher', 'headmaster'])) {
                if ($user->teacher) {
                    $user->teacher()->update($profileData);
                } else {
                    $profileData['user_id'] = $user->id;
                    $user->teacher()->create($profileData);
                }
            } elseif ($roleName === 'student') {
                if ($user->student) {
                    $user->student()->update($profileData);
                } else {
                    $profileData['user_id'] = $user->id;
                    $user->student()->create($profileData);
                }
            }
        }

        return $this->getUserById($user->id);
    }

    public function toggleStatus($id)
    {
        return $this->userRepository->toggleStatus($id);
    }

    public function deleteUser($id)
    {
        return $this->userRepository->deleteUser($id);
    }
}
