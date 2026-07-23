<?php

namespace App\Models\Finance;

use App\Models\Student\Student;
use Illuminate\Database\Eloquent\Model;

class ScholarshipRecipient extends Model
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
    protected $table = 'scholarship_recipients';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'start_date',
        'end_date',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scholarship_id' => 'integer',
            'student_id' => 'integer',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class, 'scholarship_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
