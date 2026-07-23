<?php

namespace App\Models\Finance;

use App\Models\Core\User;
use Illuminate\Database\Eloquent\Model;

class FinanceTransaction extends Model
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
    protected $table = 'finance_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'transaction_number',
        'category',
        'transaction_date',
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
            'transaction_date' => 'datetime',
            'recorded_by' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
