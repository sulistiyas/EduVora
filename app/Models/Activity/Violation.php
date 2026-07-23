<?php

namespace App\Models\Activity;

use App\Models\Core\User;
use App\Models\Student\Student;
use Illuminate\Database\Eloquent\Model;

class Violation extends Model
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
    protected $table = 'violations';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'description',
        'violation_date',
        'resolution',
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
            'violation_type_id' => 'integer',
            'reported_by' => 'integer',
            'violation_date' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function violationtype()
    {
        return $this->belongsTo(ViolationType::class, 'violation_type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
