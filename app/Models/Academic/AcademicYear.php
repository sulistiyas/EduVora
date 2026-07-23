<?php

namespace App\Models\Academic;

use App\Models\Core\SchoolProfiles;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'academic_year_id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'academic_years';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'academic_year_id',
        'school_id',
        'academic_year_name',
        'start_date',
        'end_date',
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
            'start_date' => 'date:Y-m-d',
            'end_date' => 'date:Y-m-d',
            'status' => 'string',
        ];
    }

    public function schoolProfile()
    {
        return $this->belongsTo(SchoolProfiles::class, 'school_id', 'school_id');
    }

    public function semesters()
    {
        return $this->hasMany(Semester::class, 'academic_year_id', 'academic_year_id');
    }
}
