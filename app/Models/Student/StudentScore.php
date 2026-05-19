<?php

namespace App\Models\Student;

use Illuminate\Database\Eloquent\Model;

class StudentScore extends Model
{
    /**
     * Primary key.
     *
     * @var string
     */
    protected $primaryKey = 'student_score_id';

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'student_scores';

    /**
     * Mass assignable attributes.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'grade_subject_id',
        'semester_id',
        'score_type',
        'score',
        'max_score',
        'description',
        'date',
    ];

    /**
     * Attribute casting.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'student_id'       => 'integer',
            'grade_subject_id' => 'integer',
            'semester_id'      => 'integer',
            'score'            => 'decimal:2',
            'max_score'        => 'decimal:2',
            'date'             => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->belongsTo(
            \App\Models\Student\Student::class,
            'student_id'
        );
    }

    public function gradeSubject()
    {
        return $this->belongsTo(
            \App\Models\Academic\GradeSubject::class,
            'grade_subject_id'
        );
    }

    public function semester()
    {
        return $this->belongsTo(
            \App\Models\Academic\Semester::class,
            'semester_id'
        );
    }
}