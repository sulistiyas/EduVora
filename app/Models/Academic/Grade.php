<?php

namespace App\Models\Academic;

use App\Models\Core\SchoolProfiles;
use App\Models\Student\Student;
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
        // 'grade_id',
        'school_id',
        'academic_year_id',
        'room_id',
        'homeroom_teacher_id',
        'grade_name',
        'level',
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

    public function gradeSubjects()
    {
        return $this->hasMany(GradeSubject::class, 'grade_id', 'grade_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'grade_subjects',
            'grade_id',
            'subject_id',
            'grade_id',
            'id'
        )->withPivot([
            'teacher_id',
            'kkm',
            'weight_harian',
            'weight_uts',
            'weight_uas',
            'status',
        ])->withTimestamps();
    }

    public function school()
    {
        return $this->belongsTo(
            SchoolProfiles::class,
            'school_id',
            'school_id'
        );
    }

    public function students()
    {
        return $this->hasMany(
            Student::class,
            'grade_id',
            'grade_id'
        );
    }
}
