<?php

namespace App\Models\Exam;

use App\Models\Academic\Grade;
use App\Models\Academic\Subject;
use App\Models\Core\SchoolProfiles;
use App\Models\Teacher\Teacher;
use Illuminate\Database\Eloquent\Builder;
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
        'school_id',
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
            'subject_id' => 'integer',
            'grade_id' => 'integer',
            'teacher_id' => 'integer',
            'assigned_date' => 'datetime',
            'due_date' => 'datetime',
        ];
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function school()
    {
        return $this->belongsTo(SchoolProfiles::class, 'school_id');
    }

    public function submissions()
    {
        return $this->hasMany(AssigmentSubmission::class, 'assigment_id');
    }

    public function scopeForSchool(Builder $query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast();
    }

    public function submissionCount()
    {
        return $this->submissions()->where('status', '!=', 'pending')->count();
    }

    public function gradedCount()
    {
        return $this->submissions()->where('status', 'graded')->count();
    }
}
