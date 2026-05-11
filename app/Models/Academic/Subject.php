<?php

namespace App\Models\Academic;

use App\Models\Core\SchoolProfiles;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
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
    protected $table = 'subjects';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'school_id',
        'subject_name',
        'subject_code',
        'category',
        'credits',
        'hours_per_week',
        'description',
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
            
        ];
    }

    public function gradeSubjects()
    {
        return $this->hasMany(GradeSubject::class);
    }

    public function grades()
    {
        return $this->belongsToMany(
            Grade::class,
            'grade_subjects',
            'subject_id',
            'grade_id',
            'id',
            'grade_id'
        )->withPivot([
            'teacher_id',
            'kkm',
            'weight_harian',
            'weight_uts',
            'weight_uas',
            'status'
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
}