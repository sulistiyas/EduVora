<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class FeeSetting extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fee_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fee_type_id' => 'integer',
        'grade_id' => 'integer',
        'academic_year_id' => 'integer',
        ];
    }


    public function feetype() { return $this->belongsTo(\App\Models\Finance\FeeType::class, 'fee_type_id'); }

    public function grade() { return $this->belongsTo(\App\Models\Academic\Grade::class, 'grade_id'); }

    public function academicyear() { return $this->belongsTo(\App\Models\Academic\AcademicYear::class, 'academic_year_id'); }
}