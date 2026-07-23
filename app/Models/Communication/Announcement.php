<?php

namespace App\Models\Communication;

use App\Models\Core\User;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
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
    protected $table = 'announcements';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'content',
        'publish_date',
        'expired_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'posted_by' => 'integer',
            'publish_date' => 'datetime',
            'expired_date' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
