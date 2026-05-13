<?php

namespace App\Models\Academic;

use App\Models\Core\SchoolProfiles;
use App\Models\Teacher\Teacher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    /**
     * Primary Key
     */
    protected $primaryKey = 'schedule_id';

    /**
     * Table
     */
    protected $table = 'schedules';

    /**
     * Mass Assignable
     */
    protected $fillable = [
        'school_id',
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
     */
    protected function casts(): array
    {
        return [
            'school_id'        => 'integer',
            'grade_subject_id' => 'integer',
            'room_id'          => 'integer',
            'semester_id'      => 'integer',
            'day_of_week'      => 'integer',
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
     * School Relation
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(
            SchoolProfiles::class,
            'school_id'
        );
    }

    /**
     * Grade Subject Relation
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
     * Day Name
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
     * Time Range
     */
    public function getTimeRangeAttribute(): string
    {
        return substr($this->start_time, 0, 5)
            . ' - ' .
            substr($this->end_time, 0, 5);
    }

    /**
     * Shortcut Grade
     */
    public function getGradeAttribute()
    {
        return $this->gradeSubject?->grade;
    }

    /**
     * Shortcut Subject
     */
    public function getSubjectAttribute()
    {
        return $this->gradeSubject?->subject;
    }

    /**
     * Shortcut Teacher
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
        return $query->where(
            'status',
            self::STATUS_ACTIVE
        );
    }

    /**
     * Filter By School
     */
    public function scopeSchool(
        Builder $query,
        int $schoolId
    ): Builder {
        return $query->where(
            'school_id',
            $schoolId
        );
    }

    /**
     * Filter By Day
     */
    public function scopeDay(
        Builder $query,
        int $day
    ): Builder {
        return $query->where(
            'day_of_week',
            $day
        );
    }

    /**
     * Filter By Semester
     */
    public function scopeSemester(
        Builder $query,
        int $semesterId
    ): Builder {
        return $query->where(
            'semester_id',
            $semesterId
        );
    }

    /**
     * Order By Time
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
     * Check Active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check Lab Session
     */
    public function isLabSession(): bool
    {
        return $this->session_type === self::SESSION_LAB;
    }

    /**
     * Check Time Overlap
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