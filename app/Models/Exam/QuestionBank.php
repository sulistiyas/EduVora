<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
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
    protected $table = 'question_banks';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'content',
        'options',
        'answer_key',
        'difficulty',
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
        'teacher_id' => 'integer',
        ];
    }


    public function subject() { return $this->belongsTo(\App\Models\Academic\Subject::class, 'subject_id'); }

    public function teacher() { return $this->belongsTo(\App\Models\Teacher\Teacher::class, 'teacher_id'); }
}