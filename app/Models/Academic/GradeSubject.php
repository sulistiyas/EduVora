<?php

namespace App\Models\Academic;

use App\Models\Teacher\Teacher;
use Illuminate\Database\Eloquent\Model;

class GradeSubject extends Model
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
    protected $table = 'grade_subjects';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'grade_id',
        'subject_id',
        'teacher_id',
        'kkm',
        'weight_harian',
        'weight_uts',
        'weight_uas',
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
            'grade_id' => 'integer',
            'subject_id' => 'integer',
            'teacher_id' => 'integer',
            'kkm' => 'integer',
            'weight_harian' => 'integer',
            'weight_uts' => 'integer',
            'weight_uas' => 'integer',
        ];
    }


    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id', 'grade_id');
    }

    public function subject()
    {
        return $this->belongsTo(
            Subject::class,
            'subject_id',
            'id'
        );
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'teacher_id');
    }
}