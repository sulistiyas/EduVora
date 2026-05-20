<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'semester_id';

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
        'status',
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
            'start_date' => 'date:Y-m-d',
            'end_date' => 'date:Y-m-d',
            'midterm_start_date' => 'date:Y-m-d',
            'midterm_end_date' => 'date:Y-m-d',
            'final_start_date' => 'date:Y-m-d',
            'final_end_date' => 'date:Y-m-d',
        ];
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'academic_year_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}