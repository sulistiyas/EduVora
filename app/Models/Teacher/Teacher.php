<?php

namespace App\Models\Teacher;

use App\Models\Academic\Subject;
use App\Models\Core\User;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'teacher_id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'teachers';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'teacher_id',
        'user_id',
        'nip',
        'nik',
        'full_name',
        'birth_place',
        'birth_date',
        'gender',
        'religion',
        'address',
        'phone',
        'email',
        'employee_status',
        'position',
        'grade_level',
        'education_level',
        'major',
        'certification',
        'npwp',
        'join_date',
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
            'user_id' => 'integer',
            'birth_date' => 'datetime',
            'join_date' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'grade_subjects',
            'teacher_id',
            'subject_id',
            'teacher_id',
            'id'
        );
    }
}
