<?php

namespace App\Models\Exam;

use App\Models\Academic\Subject;
use Illuminate\Database\Eloquent\Model;

class ReportCardDetail extends Model
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

    public function reportcard()
    {
        return $this->belongsTo(ReportCard::class, 'report_card_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
