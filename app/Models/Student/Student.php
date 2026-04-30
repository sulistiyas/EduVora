<?php

namespace App\Models\Student;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'students';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nis',
        'full_name',
        'nick_name',
        'email',
        'birth_date',
        'phone_number',
        'address',
        'city',
        'province',
        'postal_code',
        'profile_photo',
        'class_group',
        'enrollment_date',
        'graduation_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'school_id' => 'integer',
        'birth_date' => 'datetime',
        'grade_id' => 'integer',
        'enrollment_date' => 'datetime',
        'graduation_date' => 'datetime',
        ];
    }


    public function schoolprofiles() { return $this->belongsTo(\App\Models\Core\SchoolProfiles::class, 'school_id'); }

    public function grade() { return $this->belongsTo(\App\Models\Academic\Grade::class, 'grade_id'); }
}