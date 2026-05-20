<?php

namespace App\Models\Student;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoreDetail extends Model
{
    protected $primaryKey = 'score_detail_id';

    protected $fillable = [
        'score_session_id',
        'student_id',
        'score',
        'max_score',
        'notes',
    ];

    protected $casts = [
        'score'     => 'decimal:2',
        'max_score' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function scoreSession(): BelongsTo
    {
        return $this->belongsTo(
            ScoreSession::class,
            'score_session_id',
            'score_session_id'
        );
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(
            Student::class,
            'student_id',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function getPercentageAttribute(): float
    {
        if ($this->max_score <= 0) {
            return 0;
        }

        return round(
            ($this->score / $this->max_score) * 100,
            2
        );
    }
}