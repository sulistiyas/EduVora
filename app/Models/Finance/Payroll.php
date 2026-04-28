<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payrolls';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'month',
        'year',
        'payment_date',
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
        'year' => 'integer',
        'payment_date' => 'datetime',
        ];
    }


    public function teacher() { return $this->belongsTo(\App\Models\Teacher\Teacher::class, 'teacher_id'); }
}