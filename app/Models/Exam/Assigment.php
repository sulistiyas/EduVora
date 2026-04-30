<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Model;

class Assigment extends Model
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
    protected $table = 'assigments';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'assigned_date',
        'due_date',
        'attachment',
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
        'teacher_id' => 'integer',
        'assigned_date' => 'datetime',
        'due_date' => 'datetime',
        ];
    }


    public function subject() { return $this->belongsTo(\App\Models\Academic\Subject::class, 'subject_id'); }

    public function grade() { return $this->belongsTo(\App\Models\Academic\Grade::class, 'grade_id'); }

    public function teacher() { return $this->belongsTo(\App\Models\Teacher\Teacher::class, 'teacher_id'); }
}