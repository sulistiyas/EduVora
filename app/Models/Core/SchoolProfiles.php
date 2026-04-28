<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class SchoolProfiles extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'school_profiles';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'school_id',
        'school_name',
        'npsn',
        'nss',
        'contact_email',
        'contact_phone',
        'website',
        'address',
        'province',
        'city',
        'district',
        'postal_code',
        'logo',
        'headmaster_name',
        'headmaster_nip',
        'kkm_default',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            
        ];
    }
}