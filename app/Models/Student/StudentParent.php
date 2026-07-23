<?php

namespace App\Models\Student;

use Illuminate\Database\Eloquent\Model;

class StudentParent extends Model
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
    protected $table = 'student_parents';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'parent_name',
        'email',
        'phone_number',
        'occupation',
        'address',
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
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
