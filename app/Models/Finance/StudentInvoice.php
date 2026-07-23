<?php

namespace App\Models\Finance;

use App\Models\Student\Student;
use Illuminate\Database\Eloquent\Model;

class StudentInvoice extends Model
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
    protected $table = 'student_invoices';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'invoice_number',
        'due_date',
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
            'student_id' => 'integer',
            'fee_setting_id' => 'integer',
            'due_date' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function feesetting()
    {
        return $this->belongsTo(FeeSetting::class, 'fee_setting_id');
    }
}
