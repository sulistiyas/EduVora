<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Model;

class AssigmentSubmission extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'assigment_submissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'submitted_at',
        'file_path',
        'feedback',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'assigment_id' => 'integer',
        'student_id' => 'integer',
        ];
    }


    public function assigment() { return $this->belongsTo(\App\Models\Exam\Assigment::class, 'assigment_id'); }

    public function student() { return $this->belongsTo(\App\Models\Student\Student::class, 'student_id'); }
}