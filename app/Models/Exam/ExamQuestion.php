<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'exam_questions';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'exam_id' => 'integer',
        'question_bank_id' => 'integer',
        ];
    }


    public function exam() { return $this->belongsTo(\App\Models\Exam\Exam::class, 'exam_id'); }

    public function questionbank() { return $this->belongsTo(\App\Models\Exam\QuestionBank::class, 'question_bank_id'); }
}