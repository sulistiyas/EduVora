<?php

namespace App\Services\Academic;

use App\Models\Academic\Schedule;
use App\Models\Activity\AttendanceSession;
use App\Repositories\Academic\AttendanceRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    public function __construct(
        protected AttendanceRepository $repo
    ) {}

    /*
    |--------------------------------------------------------------------------
    | SESSION
    |--------------------------------------------------------------------------
    */

    /**
     * Paginate sessions for a teacher.
     */
    public function paginate(int $teacherId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repo->paginateByTeacher($teacherId, $filters, $perPage);
    }

    /**
     * Paginate sessions for an entire school (school-admin view).
     */
    public function paginateBySchool(int $schoolId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repo->paginateBySchool($schoolId, $filters, $perPage);
    }

    /**
     * Find a session or abort 404.
     */
    public function findOrFail(int $id): AttendanceSession
    {
        $session = $this->repo->findById($id);
        abort_if(is_null($session), 404, 'Sesi absensi tidak ditemukan.');

        return $session;
    }

    /**
     * Ensure teacher owns this session, abort 403 otherwise.
     */
    public function authorizeTeacher(AttendanceSession $session, int $teacherId): void
    {
        abort_if(
            $session->teacher_id !== $teacherId,
            403,
            'Anda tidak memiliki akses ke sesi ini.'
        );
    }

    /**
     * Cek apakah sudah ada session untuk schedule pada hari ini.
     */
    public function findExistingSessionToday(int $scheduleId): ?AttendanceSession
    {
        return $this->repo->findByScheduleAndDate($scheduleId, now()->toDateString());
    }

    /**
     * Get or auto-create a draft session for a schedule on today's date.
     *
     * Flow:
     *   1. Cek apakah sudah ada session untuk schedule + date ini.
     *   2. Jika ada → kembalikan session yang ada.
     *   3. Jika belum → buat session draft baru, lalu seed details
     *      dengan semua siswa di kelas (status default = H/Hadir).
     *
     * Alasan auto-seed: lebih efisien — guru tinggal ubah siswa yang
     * tidak hadir, tidak perlu isi ulang dari kosong.
     */
    public function getOrCreateSession(
        Schedule $schedule,
        int $teacherId,
        int $recordedBy,
        string $attendanceDate,   // ← wajib diisi dari luar
    ): AttendanceSession {
        $existing = $this->repo->findByScheduleAndDate($schedule->schedule_id, $attendanceDate);

        if ($existing) {
            return $existing;
        }

        $gradeSubject = $schedule->gradeSubject;
        abort_if(is_null($gradeSubject), 422, 'Jadwal tidak memiliki data kelas/mapel.');

        $session = $this->repo->createSession([
            'school_id' => Auth::user()->schools()->first()?->school_id,
            'schedule_id' => $schedule->schedule_id,
            'teacher_id' => $teacherId,
            'subject_id' => $gradeSubject->subject_id,
            'grade_id' => $gradeSubject->grade_id,
            'semester_id' => $schedule->semester_id,
            'attendance_date' => $attendanceDate,   // ← dari parameter
            'meeting_number' => $this->nextMeetingNumber(
                $schedule->schedule_id,
                $schedule->semester_id
            ),
            'status' => AttendanceSession::STATUS_DRAFT,
            'is_locked' => false,
            'recorded_by' => $recordedBy,
        ]);

        $students = $this->repo->getStudentsByGrade($gradeSubject->grade_id);
        $details = $students->map(fn ($s) => [
            'student_id' => $s->id,
            'status' => 'H',
            'note' => null,
        ])->toArray();

        $this->repo->upsertDetails($session->attendance_session_id, $details);

        return $this->repo->findById($session->attendance_session_id);
    }

    /**
     * Save attendance details (bulk upsert) for a draft session.
     * Throws ValidationException if session is locked or already approved.
     */
    // public function saveDetails(AttendanceSession $session, array $details): AttendanceSession
    // {
    //     $this->guardLocked($session);

    //     $this->repo->upsertDetails($session->attendance_session_id, $details);

    //     return $this->repo->findById($session->attendance_session_id);
    // }
    public function saveDetails(AttendanceSession $session, array $details): AttendanceSession
    {
        $this->guardLocked($session);

        // Approved tidak boleh diedit
        if ($session->isApproved()) {
            throw ValidationException::withMessages([
                'status' => 'Sesi yang sudah disetujui tidak dapat diubah.',
            ]);
        }

        $this->repo->upsertDetails(
            $session->attendance_session_id,
            $details
        );

        /**
         * Jika sebelumnya sudah submitted,
         * lalu diedit kembali saat unlocked,
         * otomatis kembali ke draft.
         */
        if ($session->status === AttendanceSession::STATUS_SUBMITTED) {

            $session = $this->repo->updateSession($session, [
                'status' => AttendanceSession::STATUS_DRAFT,
            ]);
        }

        return $this->repo->findById($session->attendance_session_id);
    }

    /**
     * Submit a session (draft → submitted).
     * Optionally saves notes at the same time.
     */
    public function submit(AttendanceSession $session, ?string $notes = null): AttendanceSession
    {
        $this->guardLocked($session);

        return $this->repo->updateSession($session, [
            'status' => AttendanceSession::STATUS_SUBMITTED,
            'notes' => $notes ?? $session->notes,
        ]);
    }

    /**
     * Lock a session (teacher self-lock after submit).
     */
    public function lock(AttendanceSession $session): AttendanceSession
    {
        if ($session->is_locked) {
            return $session;
        }

        return $this->repo->updateSession($session, ['is_locked' => true]);
    }

    /**
     * Unlock a session (only if not approved yet).
     */
    public function unlock(AttendanceSession $session): AttendanceSession
    {
        if ($session->isApproved()) {
            throw ValidationException::withMessages([
                'is_locked' => 'Sesi yang sudah disetujui tidak dapat dibuka kembali.',
            ]);
        }

        return $this->repo->updateSession($session, ['is_locked' => false]);
    }

    /*
    |--------------------------------------------------------------------------
    | STUDENTS & DETAILS
    |--------------------------------------------------------------------------
    */

    /**
     * Get students for a grade (used to render the attendance form).
     */
    public function getStudentsByGrade(int $gradeId): Collection
    {
        return $this->repo->getStudentsByGrade($gradeId);
    }

    /*
    |--------------------------------------------------------------------------
    | STATS
    |--------------------------------------------------------------------------
    */

    public function stats(int $teacherId, ?int $semesterId = null): array
    {
        return $this->repo->countByStatus($teacherId, $semesterId);
    }

    public function statsBySchool(int $schoolId, ?int $semesterId = null): array
    {
        return $this->repo->countBySchool($schoolId, $semesterId);
    }

    /*
    |--------------------------------------------------------------------------
    | RESOURCE — shape for frontend
    |--------------------------------------------------------------------------
    */

    public function toResource(AttendanceSession $session): array
    {
        return [
            'attendance_session_id' => $session->attendance_session_id,
            'schedule_id' => $session->schedule_id,
            'attendance_date' => $session->attendance_date?->toDateString(),
            'attendance_date_label' => $session->attendance_date?->translatedFormat('l, d F Y'),
            'meeting_number' => $session->meeting_number,
            'status' => $session->status,
            'is_locked' => $session->is_locked,
            'notes' => $session->notes,

            // Relations
            'subject_name' => $session->subject?->subject_name,
            'grade_name' => $session->grade?->grade_name,
            'semester_name' => $session->semester?->semester_name,
            'teacher_name' => $session->teacher?->full_name,

            // Counts (dari appends di model)
            'present_count' => $session->present_count,
            'absent_count' => $session->absent_count,
            'permission_count' => $session->permission_count,
            'sick_count' => $session->sick_count,
            'late_count' => $session->late_count,
            'total_students' => $session->details->count(),
        ];
    }

    public function toDetailResource(AttendanceSession $session): array
    {
        $base = $this->toResource($session);
        $details = $session->details->map(fn ($d) => [
            'attendance_detail_id' => $d->attendance_detail_id,
            'student_id' => $d->student_id,
            'student_name' => $d->student?->full_name,
            'nis' => $d->student?->nis,
            'photo' => $d->student?->photo,
            'status' => $d->status,
            'status_label' => $d->status_label,
            'note' => $d->note,
        ]);

        return array_merge($base, ['details' => $details]);
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Auto-increment meeting number for a schedule in a semester.
     */
    private function nextMeetingNumber(int $scheduleId, int $semesterId): int
    {
        $last = AttendanceSession::where('schedule_id', $scheduleId)
            ->where('semester_id', $semesterId)
            ->max('meeting_number');

        return ($last ?? 0) + 1;
    }

    /**
     * Throw 403 if session is locked.
     */
    private function guardLocked(AttendanceSession $session): void
    {
        if ($session->is_locked) {
            throw ValidationException::withMessages([
                'is_locked' => 'Sesi ini sudah dikunci dan tidak dapat diubah.',
            ]);
        }
    }
}
