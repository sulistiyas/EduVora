<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fee_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            
        ];
    }
}