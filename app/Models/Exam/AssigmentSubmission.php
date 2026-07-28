<?php

namespace App\Models\Exam;

use App\Models\Student\Student;
use Illuminate\Database\Eloquent\Model;

class AssigmentSubmission extends Model
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
        'status',
        'score',
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

    public function assigment()
    {
        return $this->belongsTo(Assigment::class, 'assigment_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function statusLabel()
    {
        return match ($this->status) {
            'pending' => 'Belum Dikumpulkan',
            'submitted' => 'Terkumpul',
            'graded' => 'Dinilai',
            default => 'Unknown',
        };
    }

    public function isGraded()
    {
        return $this->status === 'graded';
    }

    public function isSubmitted()
    {
        return $this->status === 'submitted' || $this->status === 'graded';
    }
}
