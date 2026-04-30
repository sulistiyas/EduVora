<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
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
    protected $table = 'exam_sessions';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'started_at',
        'finished_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'exam_id' => 'integer',
        'student_id' => 'integer',
        ];
    }


    public function exam() { return $this->belongsTo(\App\Models\Exam\Exam::class, 'exam_id'); }

    public function student() { return $this->belongsTo(\App\Models\Student\Student::class, 'student_id'); }
}