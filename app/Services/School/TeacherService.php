<?php

namespace App\Services\School;

use App\Models\Core\Role;
use App\Repositories\School\TeacherRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class TeacherService
{
    public function __construct(protected TeacherRepository $repo) {}

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

        // Attach role teacher (otomatis)
        $teacherRole = Role::where('role_name', 'teacher')->first();
        if ($teacherRole) {
            $user->roles()->attach($teacherRole->role_id);
        }

        // Attach sekolah admin yang login
        $user->schools()->sync([$schoolId]);

        // Buat profil teacher jika ada
        if ($profileData) {
            $profileData['user_id'] = $user->id;
            $user->teacher()->create($profileData);
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
            throw new \Exception('Guru tidak ditemukan.');
        }

        // Pastikan sekolah & role tidak berubah
        $user->schools()->sync([$schoolId]);
        $teacherRole = Role::where('role_name', 'teacher')->first();
        if ($teacherRole) {
            $user->roles()->sync([$teacherRole->role_id]);
        }

        // Update / buat profil teacher
        if ($profileData) {
            if ($user->teacher) {
                $user->teacher()->update($profileData);
            } else {
                $profileData['user_id'] = $user->id;
                $user->teacher()->create($profileData);
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
    //  SUBJECTS (untuk filter dropdown)
    // ─────────────────────────────────────────────────────────────
    public function getSubjects(int $schoolId): array
    {
        return $this->repo->getSubjects($schoolId);
    }

    // ─────────────────────────────────────────────────────────────
    //  STATS
    // ─────────────────────────────────────────────────────────────
    public function getStats(int $schoolId): array
    {
        $base = \App\Models\Core\User::whereHas('roles', fn($q) => $q->where('role_name', 'teacher'))
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
        $teacher = $user->teacher;

        return [
            'id'                => $user->id,
            'name'              => $user->name,
            'email'             => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'is_verified'       => !is_null($user->email_verified_at),
            'profile_picture'   => $user->profile_picture,
            'phone_number'      => $user->phone_number,
            'status'            => $user->status,
            'user_type'         => 'teacher',
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

            'profile' => $teacher ? [
                'teacher_id'    => $teacher->id,
                'nip'           => $teacher->nip,
                'full_name'     => $teacher->full_name,
                'nick_name'     => $teacher->nick_name,
                'birth_date'    => $teacher->birth_date,
                'gender'        => $teacher->gender,
                'phone_number'  => $teacher->phone_number,
                'address'       => $teacher->address,
                'city'          => $teacher->city,
                'province'      => $teacher->province,
                'postal_code'   => $teacher->postal_code,
                'profile_photo' => $teacher->profile_photo,
                'subject'       => $teacher->subject,
                'employee_type' => $teacher->employee_type,
                'status'        => $teacher->status,
                'join_date'     => $teacher->join_date,
                'resign_date'   => $teacher->resign_date,
            ] : null,
        ];
    }
}