<?php

namespace App\Models\Academic;

use App\Models\Teacher\Teacher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    /**
     * Primary Key
     *
     * @var string
     */
    protected $primaryKey = 'schedule_id';

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'schedules';

    /**
     * Mass Assignable
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'grade_subject_id',
        'room_id',
        'semester_id',
        'day_of_week',
        'start_time',
        'end_time',
        'session_type',
        'status',
    ];

    /**
     * Attribute Casting
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'grade_subject_id' => 'integer',
            'room_id'          => 'integer',
            'semester_id'      => 'integer',
            'day_of_week'      => 'integer',
            // 'start_time'       => 'datetime:H:i',
            // 'end_time'         => 'datetime:H:i',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CONSTANTS
    |--------------------------------------------------------------------------
    */

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const SESSION_REGULAR = 'regular';

    public const SESSION_LAB = 'lab';

    public const SESSION_EXAM = 'exam';

    public const SESSION_EXTRACURRICULAR = 'extracurricular';

    public const SESSION_REMEDIAL = 'remedial';

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Grade Subject Relation
     *
     * schedule
     * → grade_subject
     * → grade
     * → subject
     * → teacher
     */
    public function gradeSubject(): BelongsTo
    {
        return $this->belongsTo(
            GradeSubject::class,
            'grade_subject_id'
        );
    }

    /**
     * Room Relation
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(
            Room::class,
            'room_id'
        );
    }

    /**
     * Semester Relation
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(
            Semester::class,
            'semester_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Get day name
     */
    public function getDayNameAttribute(): string
    {
        return match ($this->day_of_week) {
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
            default => '-',
        };
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute(): string
    {
        return substr($this->start_time, 0, 5)
            . ' - ' .
            substr($this->end_time, 0, 5);
    }

    /**
     * Shortcut grade
     */
    public function getGradeAttribute()
    {
        return $this->gradeSubject?->grade;
    }

    /**
     * Shortcut subject
     */
    public function getSubjectAttribute()
    {
        return $this->gradeSubject?->subject;
    }

    /**
     * Shortcut teacher
     */
    public function getTeacherAttribute(): ?Teacher
    {
        return $this->gradeSubject?->teacher;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Active Schedule
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Filter by day
     */
    public function scopeDay(Builder $query, int $day): Builder
    {
        return $query->where('day_of_week', $day);
    }

    /**
     * Filter by semester
     */
    public function scopeSemester(
        Builder $query,
        int $semesterId
    ): Builder {
        return $query->where('semester_id', $semesterId);
    }

    /**
     * Order by schedule time
     */
    public function scopeOrderByTime(
        Builder $query
    ): Builder {
        return $query
            ->orderBy('day_of_week')
            ->orderBy('start_time');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Check if schedule is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if current session is lab
     */
    public function isLabSession(): bool
    {
        return $this->session_type === self::SESSION_LAB;
    }

    /**
     * Check if current schedule overlaps another time
     */
    public function overlaps(
        string $startTime,
        string $endTime
    ): bool {
        return (
            $this->start_time < $endTime &&
            $this->end_time > $startTime
        );
    }
}