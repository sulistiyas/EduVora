<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'payment_date',
        'payment_method',
        'reference_number',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'student_invoice_id' => 'integer',
        'payment_date' => 'datetime',
        ];
    }


    public function studentinvoice() { return $this->belongsTo(\App\Models\Finance\StudentInvoice::class, 'student_invoice_id'); }
}