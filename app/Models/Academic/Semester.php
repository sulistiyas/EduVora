<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'semesters';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'semester_id',
        'semester_name',
        'academic_year_id',
        'start_date',
        'end_date',
        'midterm_start_date',
        'midterm_end_date',
        'final_start_date',
        'final_end_date',
        'is_active',
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
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'midterm_start_date' => 'datetime',
        'midterm_end_date' => 'datetime',
        'final_start_date' => 'datetime',
        'final_end_date' => 'datetime',
        ];
    }
}