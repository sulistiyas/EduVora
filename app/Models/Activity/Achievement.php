<?php

namespace App\Models\Activity;

use App\Models\Student\Student;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
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
    protected $table = 'achievements';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'level',
        'achievement_date',
        'certificate',
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
            'extracurricular_id' => 'integer',
            'achievement_date' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function extracurriculars()
    {
        return $this->belongsTo(Extracurriculars::class, 'extracurricular_id');
    }
}
