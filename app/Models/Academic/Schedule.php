<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
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
    protected $table = 'schedules';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'day_of_week',
        'start_time',
        'end_time',
        'session_type',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'grade_id' => 'integer',
        'subject_id' => 'integer',
        'teacher_id' => 'integer',
        'room_id' => 'integer',
        'semester_id' => 'integer',
        ];
    }


    public function grade() { return $this->belongsTo(\App\Models\Academic\Grade::class, 'grade_id'); }

    public function subject() { return $this->belongsTo(\App\Models\Academic\Subject::class, 'subject_id'); }

    public function teacher() { return $this->belongsTo(\App\Models\Teacher\Teacher::class, 'teacher_id'); }

    public function room() { return $this->belongsTo(\App\Models\Academic\Room::class, 'room_id'); }

    public function semester() { return $this->belongsTo(\App\Models\Academic\Semester::class, 'semester_id'); }
}