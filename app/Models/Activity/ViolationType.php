<?php

namespace App\Models\Activity;

use Illuminate\Database\Eloquent\Model;

class ViolationType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'violation_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'point',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'point' => 'integer',
        ];
    }
}