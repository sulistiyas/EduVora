<?php

namespace App\Models\Activity;

use App\Models\Academic\Grade;
use App\Models\Academic\Schedule;
use App\Models\Academic\Semester;
use App\Models\Academic\Subject;
use App\Models\Core\SchoolProfiles;
use App\Models\Core\User;
use App\Models\Teacher\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $table = 'attendance_sessions';

    protected $primaryKey = 'attendance_session_id';

    protected $fillable = [
        'school_id',
        'schedule_id',
        'teacher_id',
        'subject_id',
        'grade_id',
        'semester_id',
        'attendance_date',
        'meeting_number',
        'status',
        'is_locked',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'is_locked' => 'boolean',
    ];

    protected $appends = [
        'present_count',
        'absent_count',
        'permission_count',
        'sick_count',
        'late_count',
    ];

    /*
    |--------------------------------------------------------------------------
    | CONSTANTS
    |--------------------------------------------------------------------------
    */

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_APPROVED = 'approved';

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function school()
    {
        return $this->belongsTo(
            SchoolProfiles::class,
            'school_id',
            'school_id'
        );
    }

    public function schedule()
    {
        return $this->belongsTo(
            Schedule::class,
            'schedule_id',
            'schedule_id'
        );
    }

    public function teacher()
    {
        return $this->belongsTo(
            Teacher::class,
            'teacher_id',
            'teacher_id'
        );
    }

    public function subject()
    {
        return $this->belongsTo(
            Subject::class,
            'subject_id',
            'id'
        );
    }

    public function grade()
    {
        return $this->belongsTo(
            Grade::class,
            'grade_id',
            'grade_id'
        );
    }

    public function semester()
    {
        return $this->belongsTo(
            Semester::class,
            'semester_id',
            'semester_id'
        );
    }

    public function recorder()
    {
        return $this->belongsTo(
            User::class,
            'recorded_by',
            'id'
        );
    }

    public function details()
    {
        return $this->hasMany(
            AttendanceDetail::class,
            'attendance_session_id',
            'attendance_session_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeBySchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByGrade($query, $gradeId)
    {
        return $query->where('grade_id', $gradeId);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getPresentCountAttribute(): int
    {
        return $this->details()
            ->where('status', 'H')
            ->count();
    }

    public function getAbsentCountAttribute(): int
    {
        return $this->details()
            ->where('status', 'A')
            ->count();
    }

    public function getPermissionCountAttribute(): int
    {
        return $this->details()
            ->where('status', 'I')
            ->count();
    }

    public function getSickCountAttribute(): int
    {
        return $this->details()
            ->where('status', 'S')
            ->count();
    }

    public function getLateCountAttribute(): int
    {
        return $this->details()
            ->where('status', 'L')
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isLocked(): bool
    {
        return $this->is_locked;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
}
