<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
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
    protected $table = 'exam_answers';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'answer_text',
        'is_correct',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'exam_session_id' => 'integer',
            'exam_question_id' => 'integer',
            'is_correct' => 'boolean',
        ];
    }

    public function examsession()
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    public function examquestion()
    {
        return $this->belongsTo(ExamQuestion::class, 'exam_question_id');
    }
}
