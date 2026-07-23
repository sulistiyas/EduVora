<?php

namespace App\Models\Activity;

use App\Models\Student\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceDetail extends Model
{
    use HasFactory;

    protected $table = 'attendance_details';

    protected $primaryKey = 'attendance_detail_id';

    protected $fillable = [
        'attendance_session_id',
        'student_id',
        'status',
        'note',
        'attachment',
        'notified_at',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
    ];

    protected $appends = [
        'status_label',
        'status_badge',
    ];

    /*
    |--------------------------------------------------------------------------
    | CONSTANTS
    |--------------------------------------------------------------------------
    */

    public const STATUS_PRESENT = 'H';

    public const STATUS_PERMISSION = 'I';

    public const STATUS_SICK = 'S';

    public const STATUS_ABSENT = 'A';

    public const STATUS_LATE = 'L';

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function session()
    {
        return $this->belongsTo(
            AttendanceSession::class,
            'attendance_session_id',
            'attendance_session_id'
        );
    }

    public function student()
    {
        return $this->belongsTo(
            Student::class,
            'student_id',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopePresent($query)
    {
        return $query->where(
            'status',
            self::STATUS_PRESENT
        );
    }

    public function scopeAbsent($query)
    {
        return $query->where(
            'status',
            self::STATUS_ABSENT
        );
    }

    public function scopeSick($query)
    {
        return $query->where(
            'status',
            self::STATUS_SICK
        );
    }

    public function scopePermission($query)
    {
        return $query->where(
            'status',
            self::STATUS_PERMISSION
        );
    }

    public function scopeLate($query)
    {
        return $query->where(
            'status',
            self::STATUS_LATE
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {

            self::STATUS_PRESENT => 'Hadir',

            self::STATUS_PERMISSION => 'Izin',

            self::STATUS_SICK => 'Sakit',

            self::STATUS_ABSENT => 'Alpha',

            self::STATUS_LATE => 'Terlambat',

            default => 'Unknown',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {

            self::STATUS_PRESENT => 'success',

            self::STATUS_PERMISSION => 'warning',

            self::STATUS_SICK => 'info',

            self::STATUS_ABSENT => 'danger',

            self::STATUS_LATE => 'secondary',

            default => 'dark',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isPresent(): bool
    {
        return $this->status === self::STATUS_PRESENT;
    }

    public function isAbsent(): bool
    {
        return $this->status === self::STATUS_ABSENT;
    }

    public function isSick(): bool
    {
        return $this->status === self::STATUS_SICK;
    }

    public function isPermission(): bool
    {
        return $this->status === self::STATUS_PERMISSION;
    }

    public function isLate(): bool
    {
        return $this->status === self::STATUS_LATE;
    }
}
