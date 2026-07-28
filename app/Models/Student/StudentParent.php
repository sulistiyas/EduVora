<?php

namespace App\Models\Student;

use App\Models\Core\User;
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
        'user_id',
        'student_id',
        'parent_name',
        'relationship',
        'email',
        'phone_number',
        'occupation',
        'address',
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
            'student_id' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
