<?php

namespace App\Models\Teacher;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'teacher_id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'teachers';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'teacher_id',
        'user_id',
        'nip',
        'nik',
        'full_name',
        'birth_place',
        'birth_date',
        'religion',
        'address',
        'phone',
        'email',
        'position',
        'grade_level',
        'major',
        'certification',
        'npwp',
        'join_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
        'birth_date' => 'datetime',
        'join_date' => 'datetime',
        ];
    }
}