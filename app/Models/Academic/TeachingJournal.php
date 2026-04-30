<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;

class TeachingJournal extends Model
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
    protected $table = 'teaching_journals';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'lesson_date',
        'topic',
        'material_covered',
        'activities',
        'reflection',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'teacher_id' => 'integer',
        'grade_subject_id' => 'integer',
        'lesson_date' => 'datetime',
        ];
    }


    public function teacher() { return $this->belongsTo(\App\Models\Teacher\Teacher::class, 'teacher_id'); }

    public function gradesubject() { return $this->belongsTo(\App\Models\Academic\GradeSubject::class, 'grade_subject_id'); }
}