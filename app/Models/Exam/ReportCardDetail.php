<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Model;

class ReportCardDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report_card_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'grade_letter',
        'remarks',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'report_card_id' => 'integer',
        'subject_id' => 'integer',
        ];
    }


    public function reportcard() { return $this->belongsTo(\App\Models\Exam\ReportCard::class, 'report_card_id'); }

    public function subject() { return $this->belongsTo(\App\Models\Academic\Subject::class, 'subject_id'); }
}