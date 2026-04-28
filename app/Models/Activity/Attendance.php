<?php

namespace App\Models\Activity;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attendances';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'attendance_date',
        'notes',
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
        'schedule_id' => 'integer',
        'subject_id' => 'integer',
        'teacher_id' => 'integer',
        'attendance_date' => 'datetime',
        ];
    }


    public function student() { return $this->belongsTo(\App\Models\Student\Student::class, 'student_id'); }

    public function schedule() { return $this->belongsTo(\App\Models\Academic\Schedule::class, 'schedule_id'); }

    public function subject() { return $this->belongsTo(\App\Models\Academic\Subject::class, 'subject_id'); }

    public function teacher() { return $this->belongsTo(\App\Models\Teacher\Teacher::class, 'teacher_id'); }
}