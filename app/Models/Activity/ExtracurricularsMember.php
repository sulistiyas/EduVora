<?php

namespace App\Models\Activity;

use Illuminate\Database\Eloquent\Model;

class ExtracurricularsMember extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'extracurriculars_members';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'join_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'extracurricular_id' => 'integer',
        'student_id' => 'integer',
        'join_date' => 'datetime',
        ];
    }


    public function extracurriculars() { return $this->belongsTo(\App\Models\Activity\Extracurriculars::class, 'extracurricular_id'); }

    public function student() { return $this->belongsTo(\App\Models\Student\Student::class, 'student_id'); }
}