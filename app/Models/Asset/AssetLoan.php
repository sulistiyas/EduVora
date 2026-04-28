<?php

namespace App\Models\Asset;

use Illuminate\Database\Eloquent\Model;

class AssetLoan extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'asset_loans';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'loan_date',
        'return_date',
        'quantity',
        'purpose',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'asset_id' => 'integer',
        'teacher_id' => 'integer',
        'loan_date' => 'datetime',
        'return_date' => 'datetime',
        'quantity' => 'integer',
        ];
    }


    public function asset() { return $this->belongsTo(\App\Models\Asset\Asset::class, 'asset_id'); }

    public function teacher() { return $this->belongsTo(\App\Models\Teacher\Teacher::class, 'teacher_id'); }
}