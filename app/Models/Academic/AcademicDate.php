<?php

namespace App\Models\Academic;

use App\Models\School\SchoolProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicDate extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'academic_date_id';

    protected $fillable = [
        'school_id',
        'semester_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'type',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    const TYPE_HOLIDAY = 'holiday';

    const TYPE_EXAM = 'exam';

    const TYPE_EVENT = 'event';

    const TYPE_DEADLINE = 'deadline';

    const TYPE_OTHER = 'other';

    const TYPE_LABELS = [
        self::TYPE_HOLIDAY => 'Libur',
        self::TYPE_EXAM => 'Ujian',
        self::TYPE_EVENT => 'Acara',
        self::TYPE_DEADLINE => 'Batas Waktu',
        self::TYPE_OTHER => 'Lainnya',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(SchoolProfile::class, 'school_id', 'school_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id', 'semester_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->type] ?? $this->type;
    }

    public function getDurationLabelAttribute(): string
    {
        if ($this->end_date && $this->end_date->ne($this->start_date)) {
            return $this->start_date->format('d M Y').' – '.$this->end_date->format('d M Y');
        }

        return $this->start_date->format('d M Y');
    }

    public function isUpcoming(): bool
    {
        return $this->start_date->isFuture();
    }

    public function isOngoing(): bool
    {
        $now = now()->toDateString();
        $end = $this->end_date?->toDateString() ?? $this->start_date->toDateString();

        return $this->start_date->toDateString() <= $now && $end >= $now;
    }
}
