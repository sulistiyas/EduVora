<?php

namespace App\Models\Core;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Core\Role;
use App\Models\Core\SchoolProfiles;
use App\Models\Student\Student;
use App\Models\Teacher\Teacher;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'profile_picture',
        'phone_number',
        'token',
        'user_agent',
        'payload',
        'last_activity',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_activity' => 'integer',
        ];
    }

    // =======================
    // RELATIONS
    // =======================

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'user_has_roles',
            'user_id',
            'role_id',
            'id',
            'role_id'
        );
    }

    public function schools()
    {
        return $this->belongsToMany(
            SchoolProfiles::class,
            'user_has_schools',
            'user_id',
            'school_id',
            'id',
            'school_id'
        );
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id', 'id');
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id', 'id');
    }

    // =======================
    // CORE HELPER
    // =======================

    public function hasRole($roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return $this->roles()
            ->whereIn('role_name', $roles)
            ->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->hasRole($roles);
    }

    public function hasAllRoles(array $roles): bool
    {
        return $this->roles()
            ->whereIn('role_name', $roles)
            ->count() === count($roles);
    }

    // =======================
    // ACCESSOR (BONUS)
    // =======================

    public function getRoleNamesAttribute()
    {
        return $this->roles->pluck('role_name');
    }
}