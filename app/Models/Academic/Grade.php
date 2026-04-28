<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'grades';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'grade_id',
        'academic_year_id',
        'room_id',
        'homeroom_teacher_id',
        'grade_name',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'academic_year_id' => 'integer',
        'room_id' => 'integer',
        'homeroom_teacher_id' => 'integer',
        ];
    }
}