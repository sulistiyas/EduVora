<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class BookCopy extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'book_copies';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'copy_number',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'book_id' => 'integer',
        ];
    }


    public function book() { return $this->belongsTo(\App\Models\Library\Book::class, 'book_id'); }
}