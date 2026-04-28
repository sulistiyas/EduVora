<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'exams';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'exam_name',
        'start_date',
        'end_date',
        'duration_minutes',
        'instructions',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subject_id' => 'integer',
        'grade_id' => 'integer',
        'semester_id' => 'integer',
        'duration_minutes' => 'integer',
        ];
    }


    public function subject() { return $this->belongsTo(\App\Models\Academic\Subject::class, 'subject_id'); }

    public function grade() { return $this->belongsTo(\App\Models\Academic\Grade::class, 'grade_id'); }

    public function semester() { return $this->belongsTo(\App\Models\Academic\Semester::class, 'semester_id'); }
}