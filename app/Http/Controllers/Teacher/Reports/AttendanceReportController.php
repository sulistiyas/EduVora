<?php

namespace App\Http\Controllers\Teacher\Reports;

use App\Exports\Teacher\Reports\AttendanceReportExport;
use App\Http\Controllers\Controller;
use App\Models\Activity\AttendanceSession;
use App\Services\Reports\Teacher\AttendanceReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceReportController extends Controller
{
    public function __construct(
        protected AttendanceReportService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $teacher  = Auth::user()->teacher;
        $schoolId = Auth::user()->schools->first()->school_id;

        $filters = $this->service->buildFilters(
            input:     $request->only([
                'semester_id',
                'grade_id',
                'subject_id',
                'status',
                'date_from',
                'date_to',
            ]),
            teacherId: $teacher->teacher_id,
            schoolId:  $schoolId,
        );

        $data = $this->service->getIndexData($filters);
        $data['status_options'] = $this->service->getStatusOptions();

        return view('reports.attendance.index', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW — detail 1 sesi
    |--------------------------------------------------------------------------
    */

    public function show(int $sessionId)
    {
        $teacher = Auth::user()->teacher;

        $session = AttendanceSession::with([
            'grade:grade_id,grade_name',
            'subject:id,subject_name',
            'semester:semester_id,semester_name',
        ])->findOrFail($sessionId);

        abort_if(
            $session->teacher_id !== $teacher->teacher_id,
            403,
            'Anda tidak memiliki akses ke sesi ini.'
        );

        $detail = $this->service->getSessionDetail($sessionId);

        return view('reports.attendance.show', compact('detail', 'session'));
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT — per sesi, dipanggil dari show.blade.php
    |--------------------------------------------------------------------------
    */

    public function export(Request $request)
    {
        $sessionId = (int) $request->query('session_id');

        // Ambil session untuk meta info
        $session = AttendanceSession::with([
            'grade:grade_id,grade_name',
            'subject:id,subject_name',
            'semester:semester_id,semester_name',
        ])->findOrFail($sessionId);

        // Pastikan sesi milik guru yang login
        $teacher = Auth::user()->teacher;
        abort_if(
            $session->teacher_id !== $teacher->teacher_id,
            403,
            'Anda tidak memiliki akses ke sesi ini.'
        );

        // Flat rows dari service
        $rows = $this->service->getExportRowsBySession($sessionId);

        // Meta untuk header excel
        $meta = [
            'title'   => 'Laporan Presensi — '
                         . ($session->grade?->grade_name   ?? '')
                         . ' · '
                         . ($session->subject?->subject_name ?? ''),
            'grade'   => $session->grade?->grade_name    ?? '-',
            'subject' => $session->subject?->subject_name ?? '-',
            'date'    => $session->attendance_date->format('d/m/Y'),
            'meeting' => $session->meeting_number,
            'teacher' => $teacher->full_name ?? '',
        ];

        // Nama file
        $filename = implode('-', array_filter([
            'presensi',
            str($meta['grade'])->slug(),
            str($meta['subject'])->slug(),
            'pertemuan-' . $session->meeting_number,
            $session->attendance_date->format('Ymd'),
        ])) . '.xlsx';

        return Excel::download(
            new AttendanceReportExport($rows, $meta),
            $filename
        );
    }
}