<?php

namespace App\Models\Student;

use App\Models\Academic\AcademicYear;
use App\Models\Academic\Grade;
use Illuminate\Database\Eloquent\Model;

class StudentGradeHistory extends Model
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
    protected $table = 'student_grade_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
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
            'grade_id' => 'integer',
            'academic_year_id' => 'integer',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function academicyear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
