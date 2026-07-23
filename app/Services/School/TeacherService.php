<?php

namespace App\Services\School;

use App\Models\Core\Role;
use App\Models\Core\User;
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
    //  SINGLE
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

        // Attach sekolah dari admin yang login (pivot: user_has_schools)
        $user->schools()->sync([$schoolId]);

        // Buat profil teacher
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

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user = $this->repo->update($id, $data);
        if (! $user) {
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
    //  EMPLOYMENT STATUSES (dropdown filter)
    // ─────────────────────────────────────────────────────────────
    public function getEmploymentStatuses(int $schoolId): array
    {
        return $this->repo->getEmploymentStatuses($schoolId);
    }

    // ─────────────────────────────────────────────────────────────
    //  STATS
    // ─────────────────────────────────────────────────────────────
    public function getStats(int $schoolId): array
    {
        $base = User::whereHas('roles', fn ($q) => $q->where('role_name', 'teacher'))
            ->whereHas('schools', fn ($q) => $q->where('school_profiles.school_id', $schoolId));

        return [
            'total' => (clone $base)->count(),
            'active' => (clone $base)->where('status', 'active')->count(),
            'inactive' => (clone $base)->where('status', 'inactive')->count(),
            'unverified' => (clone $base)->whereNull('email_verified_at')->count(),
        ];
    }

    // ─────────────────────────────────────────────────────────────
    //  FORMAT — sesuai kolom tabel teachers
    // ─────────────────────────────────────────────────────────────
    private function format(User $user): array
    {
        $t = $user->teacher; // relasi hasOne ke tabel teachers

        return [
            // ── User (accounts) ──────────────────────────────────
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'is_verified' => ! is_null($user->email_verified_at),
            'profile_picture' => $user->profile_picture,
            'phone_number' => $user->phone_number,
            'status' => $user->status,
            'user_type' => 'teacher',
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,

            'roles' => $user->roles->map(fn ($r) => [
                'role_id' => $r->role_id,
                'role_name' => $r->role_name,
            ]),

            'schools' => $user->schools->map(fn ($s) => [
                'school_id' => $s->school_id,
                'school_name' => $s->school_name,
                'school_type' => $s->school_type,
                'status' => $s->status,
            ]),

            // ── Teacher profile (tabel teachers) ─────────────────
            'profile' => $t ? [
                'teacher_id' => $t->teacher_id,
                'nip' => $t->nip,
                'nik' => $t->nik,
                'full_name' => $t->full_name,
                'birth_place' => $t->birth_place,
                'birth_date' => $t->birth_date,
                'gender' => $t->gender,
                'religion' => $t->religion,
                'address' => $t->address,
                'phone' => $t->phone,
                'email' => $t->email,
                'employment_status' => $t->employment_status,
                'position' => $t->position,
                'grade_level' => $t->grade_level,
                'education_level' => $t->education_level,
                'major' => $t->major,
                'certification' => $t->certification,
                'npwp' => $t->npwp,
                'join_date' => $t->join_date,
                'status' => $t->status,
            ] : null,
        ];
    }
}
