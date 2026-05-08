<?php

namespace App\Models\Academic;

use App\Models\Teacher\Teacher;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'grade_id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'grades';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'grade_id',
        'school_id',
        'academic_year_id',
        'room_id',
        'homeroom_teacher_id',
        'grade_name',
        'level',
        'status'
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
        'room_id' => 'integer',
        'homeroom_teacher_id' => 'integer',
        ];
    }

    public function academicYear()
    {
        return $this->belongsTo(
            AcademicYear::class,
            'academic_year_id',
            'academic_year_id'
        );
    }

    public function room()
    {
        return $this->belongsTo(
            Room::class,
            'room_id',
            'room_id'
        );
    }

    public function homeroomTeacher()
    {
        return $this->belongsTo(
            Teacher::class,
            'homeroom_teacher_id',
            'teacher_id'
        );
    }
}