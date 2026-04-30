<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable 
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'profile_picture',
        'phone_number',
        'email',
        'token',
        'user_agent',
        'payload',
        'last_activity',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'user_id' => 'integer',
            'last_activity' => 'integer',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_has_roles', 'user_id', 'role_id');
    }

    // =======================
    // CORE HELPER (REUSABLE)
    // =======================

    public function hasRole($roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return $this->roles()
            ->whereIn('role_name', $roles)
            ->exists();
    }

    // // optional: kebalikannya
    // public function hasNotRole($roles): bool
    // {
    //     return !$this->hasRole($roles);
    // }

    // // optional: harus punya semua role
    // public function hasAllRoles(array $roles): bool
    // {
    //     return $this->roles()
    //         ->whereIn('role_name', $roles)
    //         ->count() === count($roles);
    // }
}