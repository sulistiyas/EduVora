<?php

namespace App\Models\Student;

use App\Models\Academic\GradeSubject;
use App\Models\Academic\Semester;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScoreSession extends Model
{
    protected $primaryKey = 'score_session_id';

    protected $fillable = [
        'grade_subject_id',
        'semester_id',
        'score_type',
        'title',
        'description',
        'score_date',
        'is_published',
    ];

    protected $casts = [
        'score_date'   => 'date',
        'is_published' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function gradeSubject(): BelongsTo
    {
        return $this->belongsTo(
            GradeSubject::class,
            'grade_subject_id',
            'id'
        );
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(
            Semester::class,
            'semester_id',
            'semester_id'
        );
    }

    public function scoreDetails(): HasMany
    {
        return $this->hasMany(
            ScoreDetail::class,
            'score_session_id',
            'score_session_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function getAverageScoreAttribute(): float
    {
        return round(
            $this->scoreDetails()->avg('score') ?? 0,
            2
        );
    }

    public function getTotalStudentsAttribute(): int
    {
        return $this->scoreDetails()->count();
    }
}