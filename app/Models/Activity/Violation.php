<?php

namespace App\Models\Activity;

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


    public function student() { return $this->belongsTo(\App\Models\Student\Student::class, 'student_id'); }

    public function violationtype() { return $this->belongsTo(\App\Models\Activity\ViolationType::class, 'violation_type_id'); }

    public function user() { return $this->belongsTo(\App\Models\Core\User::class, 'reported_by'); }
}