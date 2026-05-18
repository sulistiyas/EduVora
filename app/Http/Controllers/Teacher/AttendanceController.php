<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Academic\Schedule;
use App\Services\Academic\AttendanceService;
use App\Services\Academic\ScheduleService;
use App\Services\SemesterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $service,
        protected ScheduleService   $scheduleService,
        protected SemesterService   $semesterService,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX — list all sessions for the logged-in teacher
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View|JsonResponse
    {
        $teacher = Auth::user()->teacher;
        abort_if(is_null($teacher), 403, 'Akun ini tidak terhubung ke data guru.');

        if ($request->expectsJson()) {
            $sessions = $this->service->paginate(
                teacherId: $teacher->teacher_id,
                filters:   $request->only([
                    'semester_id', 'grade_id', 'subject_id',
                    'status', 'date_from', 'date_to',
                ]),
                perPage: (int) $request->input('per_page', 15),
            );

            return response()->json([
                'data' => $sessions->map(fn ($s) => $this->service->toResource($s)),
                'meta' => [
                    'current_page' => $sessions->currentPage(),
                    'per_page'     => $sessions->perPage(),
                    'total'        => $sessions->total(),
                    'last_page'    => $sessions->lastPage(),
                ],
            ]);
        }

        // Pass active semester for filter default
        $activeSemester = $this->semesterService
            ->getActiveAcademicSemester(['per_page' => 'all'])
            ->first();

        $stats = $activeSemester
            ? $this->service->stats($teacher->teacher_id, $activeSemester->semester_id)
            : ['draft' => 0, 'submitted' => 0, 'approved' => 0, 'total' => 0];

        return view('pages.teacher.attendance.index', compact('activeSemester', 'stats'));
    }

    /*
    |--------------------------------------------------------------------------
    | START — called when teacher clicks "Presensi" from schedule
    |
    | GET /teacher/attendance/start?schedule_id=X
    |
    | Logic:
    |   1. Validate schedule belongs to teacher
    |   2. Get-or-create draft session for today
    |   3. Redirect to show page
    |--------------------------------------------------------------------------
    */

    public function start(Request $request): RedirectResponse
    {
        $request->validate([
            'schedule_id' => ['required', 'integer', 'exists:schedules,schedule_id'],
        ]);

        $teacher = Auth::user()->teacher;
        abort_if(is_null($teacher), 403, 'Akun ini tidak terhubung ke data guru.');

        $schedule = Schedule::with([
            'gradeSubject.subject',
            'gradeSubject.grade',
            'semester',
        ])->findOrFail($request->schedule_id);

        // Guard: jadwal harus milik teacher ini
        abort_if(
            $schedule->gradeSubject?->teacher_id !== $teacher->teacher_id,
            403,
            'Jadwal ini bukan milik Anda.'
        );

        $session = $this->service->getOrCreateSession(
            schedule:   $schedule,
            teacherId:  $teacher->teacher_id,
            recordedBy: auth()->id(),
        );

        return redirect()->route('teacher.attendance.show', $session->attendance_session_id)
            ->with('info', 'Sesi absensi ' . ($session->wasRecentlyCreated ? 'dibuat' : 'dilanjutkan') . '.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW — detail form: isi/lihat absensi per siswa
    |--------------------------------------------------------------------------
    */

    public function show(int $id): View|JsonResponse
    {
        $session = $this->service->findOrFail($id);
        $teacher = Auth::user()->teacher;

        $this->service->authorizeTeacher($session, $teacher->teacher_id);

        if (request()->expectsJson()) {
            return response()->json($this->service->toDetailResource($session));
        }

        return view('pages.teacher.attendance.show', compact('session'));
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE DETAILS — PATCH /teacher/attendance/{id}/details
    |
    | Payload: { details: [{ student_id, status, note }] }
    | Auto-saves without submitting. Supports partial update.
    |--------------------------------------------------------------------------
    */

    public function saveDetails(Request $request, int $id): JsonResponse
    {
        $session = $this->service->findOrFail($id);
        $teacher = Auth::user()->teacher;

        $this->service->authorizeTeacher($session, $teacher->teacher_id);

        $data = $request->validate([
            'details'                => ['required', 'array', 'min:1'],
            'details.*.student_id'   => ['required', 'integer', 'exists:students,id'],
            'details.*.status'       => ['required', 'in:H,I,S,A,L'],
            'details.*.note'         => ['nullable', 'string', 'max:500'],
        ]);

        $session = $this->service->saveDetails($session, $data['details']);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil disimpan.',
            'data'    => $this->service->toDetailResource($session),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SUBMIT — PATCH /teacher/attendance/{id}/submit
    |--------------------------------------------------------------------------
    */

    public function submit(Request $request, int $id): JsonResponse
    {
        $session = $this->service->findOrFail($id);
        $teacher = Auth::user()->teacher;

        $this->service->authorizeTeacher($session, $teacher->teacher_id);

        $data    = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $session = $this->service->submit($session, $data['notes'] ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil disubmit.',
            'data'    => $this->service->toResource($session),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LOCK — PATCH /teacher/attendance/{id}/lock
    |--------------------------------------------------------------------------
    */

    public function lock(int $id): JsonResponse
    {
        $session = $this->service->findOrFail($id);
        $teacher = Auth::user()->teacher;

        $this->service->authorizeTeacher($session, $teacher->teacher_id);

        $session = $this->service->lock($session);

        return response()->json([
            'success' => true,
            'message' => 'Sesi berhasil dikunci.',
            'data'    => $this->service->toResource($session),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UNLOCK — PATCH /teacher/attendance/{id}/unlock
    |--------------------------------------------------------------------------
    */

    public function unlock(int $id): JsonResponse
    {
        $session = $this->service->findOrFail($id);
        $teacher = Auth::user()->teacher;

        $this->service->authorizeTeacher($session, $teacher->teacher_id);

        $session = $this->service->unlock($session);

        return response()->json([
            'success' => true,
            'message' => 'Sesi berhasil dibuka kembali.',
            'data'    => $this->service->toResource($session),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DROPDOWN HELPERS — untuk filter di index
    |--------------------------------------------------------------------------
    */

    public function semesters(): JsonResponse
    {
        $semesters = $this->semesterService->getActiveAcademicSemester([
            'per_page'   => 'all',
            'sort_by'    => 'start_date',
            'sort_order' => 'desc',
        ]);

        return response()->json(
            $semesters->map(fn ($s) => [
                'semester_id'   => $s->semester_id,
                'semester_name' => $s->semester_name,
                'status'        => $s->status,
            ])
        );
    }
}