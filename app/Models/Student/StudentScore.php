<?php

namespace App\Models\Student;

use Illuminate\Database\Eloquent\Model;

class StudentScore extends Model
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
    protected $table = 'student_scores';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'weight',
        'remarks',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'student_id' => 'integer',
        'grade_subject_id' => 'integer',
        'semester_id' => 'integer',
        'subject_id' => 'integer',
        ];
    }


    public function student() { return $this->belongsTo(\App\Models\Student\Student::class, 'student_id'); }

    public function gradesubject() { return $this->belongsTo(\App\Models\Academic\GradeSubject::class, 'grade_subject_id'); }

    public function semester() { return $this->belongsTo(\App\Models\Academic\Semester::class, 'semester_id'); }

    public function subject() { return $this->belongsTo(\App\Models\Academic\Subject::class, 'subject_id'); }
}