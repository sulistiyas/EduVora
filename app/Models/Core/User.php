<?php

namespace App\Models\Core;

use App\Models\Student\Student;
use App\Models\Student\StudentParent;
use App\Models\Teacher\Teacher;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'profile_picture',
        'phone_number',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'status' => 'string',
        ];
    }

    // ================= RELATIONS =================

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'user_has_roles',
            'user_id',
            'role_id',
            'id',
            'role_id'
        )->withPivot('user_has_role_id')->withTimestamps();
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
        )->withTimestamps();
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id', 'id');
    }

    public function studentParent()
    {
        return $this->hasOne(StudentParent::class, 'user_id', 'id');
    }

    public function studentParents()
    {
        return $this->hasMany(StudentParent::class, 'user_id', 'id');
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id', 'id');
    }

    // ================= HELPERS =================

    public function hasRole($roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        $roleNames = [];
        $roleIds = [];

        foreach ($roles as $role) {
            if (is_numeric($role)) {
                $roleIds[] = (int) $role;
            } else {
                $roleNames[] = $role;
            }
        }

        return $this->roles()
            ->where(function ($q) use ($roleNames, $roleIds) {

                if (! empty($roleNames)) {
                    $q->whereIn('roles.role_name', $roleNames);
                }

                if (! empty($roleIds)) {
                    $q->orWhereIn('roles.role_id', $roleIds);
                }
            })
            ->exists();
    }

    public function hasAllRoles(array $roles): bool
    {
        return $this->roles()
            ->whereIn('role_name', $roles)
            ->count() === count($roles);
    }

    // ================= ACCESSOR =================

    public function getRoleNameAttribute(): ?string
    {
        return $this->relationLoaded('roles')
            ? $this->roles->first()?->role_name
            : $this->roles()->value('role_name');
    }
}
