<?php

namespace App\Models\Activity;

use Illuminate\Database\Eloquent\Model;

class CounsellingSession extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'counselling_sessions';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'session_date',
        'topic',
        'notes',
        'follow_up',
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
        'counselor_id' => 'integer',
        'session_date' => 'datetime',
        ];
    }


    public function student() { return $this->belongsTo(\App\Models\Student\Student::class, 'student_id'); }

    public function teacher() { return $this->belongsTo(\App\Models\Teacher\Teacher::class, 'counselor_id'); }
}