<?php

namespace App\Services\Reports\Teacher;

use App\Models\Activity\AttendanceDetail;
use App\Repositories\Reports\Contracts\AttendanceReportRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AttendanceReportService
{
    public function __construct(
        protected AttendanceReportRepositoryInterface $repo
    ) {}

    /*
    |--------------------------------------------------------------------------
    | FILTER BUILDER
    | Sanitize & normalize raw request input menjadi filter array standar
    |--------------------------------------------------------------------------
    */

    /**
     * Build a clean filter array from raw request input.
     * Selalu inject teacher_id & school_id dari auth context.
     *
     * @param  array  $input     — dari $request->only([...])
     * @param  int    $teacherId — dari auth teacher
     * @param  int    $schoolId  — dari auth school
     * @return array
     */
    public function buildFilters(array $input, int $teacherId, int $schoolId): array
    {
        return [
            'teacher_id'  => $teacherId,
            'school_id'   => $schoolId,
            'semester_id' => $input['semester_id'] ?? null,
            'grade_id'    => $input['grade_id']    ?? null,
            'subject_id'  => $input['subject_id']  ?? null,
            'status'      => $this->sanitizeStatus($input['status'] ?? null),
            'date_from'   => $input['date_from']   ?? null,
            'date_to'     => $input['date_to']     ?? null,
        ];
    }

    /**
     * Validate status value against allowed constants.
     */
    private function sanitizeStatus(?string $status): ?string
    {
        $allowed = [
            AttendanceDetail::STATUS_PRESENT,    // H
            AttendanceDetail::STATUS_PERMISSION, // I
            AttendanceDetail::STATUS_SICK,       // S
            AttendanceDetail::STATUS_ABSENT,     // A
            AttendanceDetail::STATUS_LATE,       // L
        ];

        return in_array($status, $allowed, true) ? $status : null;
    }

    /*
    |--------------------------------------------------------------------------
    | PAGE DATA
    | Single method untuk index page — ambil semua data yang dibutuhkan view
    |--------------------------------------------------------------------------
    */

    /**
     * Get all data needed for the attendance report index page.
     *
     * Returns:
     *   - sessions    : LengthAwarePaginator
     *   - summary     : array  (aggregate counts untuk cards)
     *   - grades      : Collection (dropdown options)
     *   - subjects    : Collection (dropdown options)
     *   - filters     : array  (active filters — dikirim balik ke view)
     *
     * @param  array  $filters
     * @param  int    $perPage
     * @return array
     */
    public function getIndexData(array $filters, int $perPage = 15): array
    {
        return [
            'sessions' => $this->repo->getSessions($filters, $perPage),
            'summary'  => $this->getFormattedSummary($filters),
            'grades'   => $this->repo->getGradeOptions($filters['teacher_id'], $filters['school_id']),
            'subjects' => $this->repo->getSubjectOptions($filters['teacher_id'], $filters['school_id']),
            'filters'  => $filters,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | SUMMARY CARDS
    |--------------------------------------------------------------------------
    */

    /**
     * Get aggregate summary with percentage calculations.
     * Adds: attendance_rate, absence_rate — siap render di summary cards.
     *
     * @param  array  $filters
     * @return array
     */
    public function getFormattedSummary(array $filters): array
    {
        $raw = $this->repo->getAggregateSummary($filters);

        $totalDetail = $raw['total_present']
            + $raw['total_permission']
            + $raw['total_sick']
            + $raw['total_absent']
            + $raw['total_late'];

        $raw['total_detail']     = $totalDetail;
        $raw['attendance_rate']  = $totalDetail > 0
            ? round(($raw['total_present'] / $totalDetail) * 100, 1)
            : 0;
        $raw['absence_rate']     = $totalDetail > 0
            ? round(($raw['total_absent'] / $totalDetail) * 100, 1)
            : 0;

        return $raw;
    }

    /*
    |--------------------------------------------------------------------------
    | STUDENT SUMMARY (tab/view per-siswa)
    |--------------------------------------------------------------------------
    */

    /**
     * Get per-student summary with attendance rate per student.
     *
     * @param  array  $filters
     * @return Collection
     */
    public function getStudentSummary(array $filters): Collection
    {
        return $this->repo
            ->getStudentSummary($filters)
            ->map(function ($row) {
                $total = $row->total_sessions > 0 ? $row->total_sessions : 1;

                $row->attendance_rate = round(
                    ($row->total_present / $total) * 100,
                    1
                );

                $row->absence_rate = round(
                    ($row->total_absent / $total) * 100,
                    1
                );

                return $row;
            });
    }

    /*
    |--------------------------------------------------------------------------
    | SESSION DETAIL
    |--------------------------------------------------------------------------
    */

    /**
     * Get detail rows for a single session, grouped by status for easy rendering.
     *
     * @param  int  $sessionId
     * @return array
     */
    public function getSessionDetail(int $sessionId): array
    {
        $details = $this->repo->getDetailsBySession($sessionId);

        return [
            'all'        => $details,
            'present'    => $details->where('status', AttendanceDetail::STATUS_PRESENT),
            'permission' => $details->where('status', AttendanceDetail::STATUS_PERMISSION),
            'sick'       => $details->where('status', AttendanceDetail::STATUS_SICK),
            'absent'     => $details->where('status', AttendanceDetail::STATUS_ABSENT),
            'late'       => $details->where('status', AttendanceDetail::STATUS_LATE),
            'counts'     => [
                'present'    => $details->where('status', AttendanceDetail::STATUS_PRESENT)->count(),
                'permission' => $details->where('status', AttendanceDetail::STATUS_PERMISSION)->count(),
                'sick'       => $details->where('status', AttendanceDetail::STATUS_SICK)->count(),
                'absent'     => $details->where('status', AttendanceDetail::STATUS_ABSENT)->count(),
                'late'       => $details->where('status', AttendanceDetail::STATUS_LATE)->count(),
                'total'      => $details->count(),
            ],
        ];
    }

    public function getExportRowsBySession(int $sessionId): Collection
    {
        $details = $this->repo->getDetailsBySession($sessionId);

        return $details->map(function ($detail) {
            return [
                'student_name'   => $detail->student?->full_name  ?? '-',
                'nis'            => $detail->student?->nis         ?? '-',
                'status_label'   => $detail->status_label,
                'note'           => $detail->note                  ?? '',
                'notified_at'    => $detail->notified_at
                                    ? $detail->notified_at->format('d/m/Y H:i')
                                    : '-',
                'meeting_number' => $detail->session?->meeting_number ?? '-',
            ];
        });
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT
    |--------------------------------------------------------------------------
    */

    /**
     * Flatten sessions + details menjadi array of rows siap export.
     * Setiap baris = 1 student x 1 session.
     *
     * Columns:
     *   date | meeting_number | grade | subject | student_name | nis | status | status_label | note
     *
     * @param  array  $filters
     * @return Collection
     */
    // public function getExportRows(array $filters): Collection
    // {
    //     $sessions = $this->repo->getSessionsForExport($filters);

    //     return $sessions->flatMap(function ($session) {
    //         return $session->details->map(function ($detail) use ($session) {
    //             return [
    //                 'date'           => $session->attendance_date->format('d/m/Y'),
    //                 'meeting_number' => $session->meeting_number,
    //                 'grade'          => $session->grade?->grade_name ?? '-',
    //                 'subject'        => $session->subject?->subject_name ?? '-',
    //                 'student_name'   => $detail->student?->full_name ?? '-',
    //                 'nis'            => $detail->student?->nis ?? '-',
    //                 'status'         => $detail->status,
    //                 'status_label'   => $detail->status_label,
    //                 'note'           => $detail->note ?? '',
    //             ];
    //         });
    //     });
    // }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Status options untuk filter dropdown di view.
     *
     * @return array
     */
    public function getStatusOptions(): array
    {
        return [
            AttendanceDetail::STATUS_PRESENT    => 'Hadir',
            AttendanceDetail::STATUS_PERMISSION => 'Izin',
            AttendanceDetail::STATUS_SICK       => 'Sakit',
            AttendanceDetail::STATUS_ABSENT     => 'Alpha',
            AttendanceDetail::STATUS_LATE       => 'Terlambat',
        ];
    }
}