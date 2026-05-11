<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\SchoolProfiles;

class Room extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'room_id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rooms';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'room_id',
        'school_id',
        'room_name',
        'code',
        'type',
        'floor',
        'building',
        'capacity',
        'facility',
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
            
        ];
    }

    public function school()
    {
        return $this->belongsTo(
            SchoolProfiles::class,
            'school_id',
            'school_id'
        );
    }

    public function grades()
    {
        return $this->hasMany(
            Grade::class,
            'room_id',
            'room_id'
        );
    }
}