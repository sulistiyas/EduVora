<?php

namespace App\Models\Activity;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
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
    protected $table = 'leave_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'start_date',
        'end_date',
        'reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'teacher_id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'approved_by' => 'integer',
        ];
    }


    public function teacher() { return $this->belongsTo(\App\Models\Teacher\Teacher::class, 'teacher_id'); }

    public function user() { return $this->belongsTo(\App\Models\Core\User::class, 'approved_by'); }
}