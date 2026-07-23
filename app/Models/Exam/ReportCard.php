<?php

namespace App\Models\Exam;

use App\Models\Academic\Grade;
use App\Models\Academic\Semester;
use App\Models\Student\Student;
use Illuminate\Database\Eloquent\Model;

class ReportCard extends Model
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
    protected $table = 'report_cards';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'comments',
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
            'semester_id' => 'integer',
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

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
