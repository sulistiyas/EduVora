<?php

namespace App\Services\School;

use App\Models\Core\Role;
use App\Repositories\School\StudentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class StudentService
{
    public function __construct(protected StudentRepository $repo) {}

    // ─────────────────────────────────────────────────────────────
    //  LIST
    // ─────────────────────────────────────────────────────────────
    public function getAll(int $schoolId, array $filters = []): LengthAwarePaginator|Collection
    {
        return $this->repo->getAll($schoolId, $filters);
    }

    // ─────────────────────────────────────────────────────────────
    //  SINGLE (formatted array)
    // ─────────────────────────────────────────────────────────────
    public function getById(int $schoolId, $id): array
    {
        $user = $this->repo->findById($schoolId, $id);
        return $this->format($user);
    }

    // ─────────────────────────────────────────────────────────────
    //  CREATE
    // ─────────────────────────────────────────────────────────────
    public function create(int $schoolId, array $data): array
    {
        $profileData = $data['profile'] ?? null;
        unset($data['profile'], $data['password_confirmation']);
        $data['password'] = Hash::make($data['password']);

        // Buat user
        $user = $this->repo->create($data);

        // Attach role student (otomatis)
        $studentRole = Role::where('role_name', 'student')->first();
        if ($studentRole) {
            $user->roles()->attach($studentRole->role_id);
        }

        // Attach sekolah admin yang login
        $user->schools()->sync([$schoolId]);

        // Buat profil student jika ada
        if ($profileData) {
            $profileData['user_id'] = $user->id;
            $user->student()->create($profileData);
        }

        return $this->getById($schoolId, $user->id);
    }

    // ─────────────────────────────────────────────────────────────
    //  UPDATE
    // ─────────────────────────────────────────────────────────────
    public function update(int $schoolId, $id, array $data): array
    {
        $profileData = $data['profile'] ?? null;
        unset($data['profile'], $data['password_confirmation']);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user = $this->repo->update($id, $data);
        if (!$user) {
            throw new \Exception('Siswa tidak ditemukan.');
        }

        // Pastikan sekolah & role tidak berubah
        $user->schools()->sync([$schoolId]);
        $studentRole = Role::where('role_name', 'student')->first();
        if ($studentRole) {
            $user->roles()->sync([$studentRole->role_id]);
        }

        // Update / buat profil student
        if ($profileData) {
            if ($user->student) {
                $user->student()->update($profileData);
            } else {
                $profileData['user_id'] = $user->id;
                $user->student()->create($profileData);
            }
        }

        return $this->getById($schoolId, $user->id);
    }

    // ─────────────────────────────────────────────────────────────
    //  TOGGLE STATUS
    // ─────────────────────────────────────────────────────────────
    public function toggleStatus($id)
    {
        return $this->repo->toggleStatus($id);
    }

    // ─────────────────────────────────────────────────────────────
    //  DELETE
    // ─────────────────────────────────────────────────────────────
    public function delete($id): bool
    {
        return $this->repo->delete($id);
    }

    // ─────────────────────────────────────────────────────────────
    //  CLASS GROUPS (untuk filter dropdown)
    // ─────────────────────────────────────────────────────────────
    public function getClassGroups(int $schoolId): array
    {
        return $this->repo->getClassGroups($schoolId);
    }

    // ─────────────────────────────────────────────────────────────
    //  STATS
    // ─────────────────────────────────────────────────────────────
    public function getStats(int $schoolId): array
    {
        $base = \App\Models\Core\User::whereHas('roles', fn($q) => $q->where('role_name', 'student'))
            ->whereHas('schools', fn($q) => $q->where('school_profiles.school_id', $schoolId));

        return [
            'total'      => (clone $base)->count(),
            'active'     => (clone $base)->where('status', 'active')->count(),
            'inactive'   => (clone $base)->where('status', 'inactive')->count(),
            'unverified' => (clone $base)->whereNull('email_verified_at')->count(),
        ];
    }

    // ─────────────────────────────────────────────────────────────
    //  FORMAT
    // ─────────────────────────────────────────────────────────────
    private function format(\App\Models\Core\User $user): array
    {
        $student = $user->student;

        return [
            'id'                => $user->id,
            'name'              => $user->name,
            'email'             => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'is_verified'       => !is_null($user->email_verified_at),
            'profile_picture'   => $user->profile_picture,
            'phone_number'      => $user->phone_number,
            'status'            => $user->status,
            'user_type'         => 'student',
            'created_at'        => $user->created_at,
            'updated_at'        => $user->updated_at,

            'roles' => $user->roles->map(fn($r) => [
                'role_id'   => $r->role_id,
                'role_name' => $r->role_name,
            ]),

            'schools' => $user->schools->map(fn($s) => [
                'school_id'   => $s->school_id,
                'school_name' => $s->school_name,
                'school_type' => $s->school_type,
                'status'      => $s->status,
            ]),

            'profile' => $student ? [
                'student_id'      => $student->id,
                'nis'             => $student->nis,
                'full_name'       => $student->full_name,
                'nick_name'       => $student->nick_name,
                'email'           => $student->email,
                'birth_date'      => $student->birth_date,
                'gender'          => $student->gender,
                'phone_number'    => $student->phone_number,
                'address'         => $student->address,
                'city'            => $student->city,
                'province'        => $student->province,
                'postal_code'     => $student->postal_code,
                'profile_photo'   => $student->profile_photo,
                'grade_id'        => $student->grade_id,
                'class_group'     => $student->class_group,
                'status'          => $student->status,
                'enrollment_date' => $student->enrollment_date,
                'graduation_date' => $student->graduation_date,
            ] : null,
        ];
    }
}