<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'subject',
        'content',
        'is_read',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sender_id' => 'integer',
        'receiver_id' => 'integer',
        'is_read' => 'boolean',
        ];
    }


    public function user() { return $this->belongsTo(\App\Models\Core\User::class, 'sender_id'); }

    public function user() { return $this->belongsTo(\App\Models\Core\User::class, 'receiver_id'); }
}