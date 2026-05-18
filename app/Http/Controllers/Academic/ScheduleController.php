<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Academic\GradeSubject;
use App\Models\Academic\Room;
use App\Models\Academic\Schedule;
use App\Models\Academic\Semester;
use App\Services\Academic\ScheduleService;
use App\Services\SemesterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function __construct(
        protected ScheduleService $service,
        protected SemesterService $semesterService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Index — datatable view + JSON list
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {
            $schedules = $this->service->paginate(
                filters: $request->only(['search', 'status', 'semester_id', 'day_of_week', 'session_type']),
                perPage: (int) $request->input('per_page', 10),
            );

            return response()->json([
                'data' => $schedules->map(fn ($s) => $this->service->toResource($s)),
                'meta' => [
                    'current_page' => $schedules->currentPage(),
                    'per_page'     => $schedules->perPage(),
                    'total'        => $schedules->total(),
                    'last_page'    => $schedules->lastPage(),
                ],
            ]);
        }

        return view('pages.academic.schedule.index');
    }

    /*
    |--------------------------------------------------------------------------
    | Show — single resource (for edit modal pre-fill)
    |--------------------------------------------------------------------------
    */

    public function show(int $id): JsonResponse
    {
        $schedule = $this->service->findOrFail($id);

        return response()->json($this->service->toResource($schedule));
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'grade_subject_id' => ['required', 'integer', 'exists:grade_subjects,id'],
            'room_id'          => ['required', 'integer', 'exists:rooms,room_id'],
            'semester_id'      => ['required', 'integer', 'exists:semesters,semester_id'],
            'day_of_week'      => ['required', 'integer', 'between:1,7'],
            'start_time'       => ['required', 'date_format:H:i'],
            'end_time'         => ['required', 'date_format:H:i', 'after:start_time'],
            'session_type'     => ['required', Rule::in([
                Schedule::SESSION_REGULAR,
                Schedule::SESSION_LAB,
                Schedule::SESSION_EXAM,
                Schedule::SESSION_EXTRACURRICULAR,
                Schedule::SESSION_REMEDIAL,
            ])],
            'status'           => ['sometimes', Rule::in([
                Schedule::STATUS_ACTIVE,
                Schedule::STATUS_INACTIVE,
            ])],
        ]);

        $data['status'] = $data['status'] ?? Schedule::STATUS_ACTIVE;

        $schedule = $this->service->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Schedule berhasil ditambahkan.',
            'data'    => $this->service->toResource($schedule),
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, int $id): JsonResponse
    {
        $schedule = $this->service->findOrFail($id);

        $data = $request->validate([
            'grade_subject_id' => ['required', 'integer', 'exists:grade_subjects,id'],
            'room_id'          => ['required', 'integer', 'exists:rooms,room_id'],
            'semester_id'      => ['required', 'integer', 'exists:semesters,semester_id'],
            'day_of_week'      => ['required', 'integer', 'between:1,7'],
            'start_time'       => ['required', 'date_format:H:i'],
            'end_time'         => ['required', 'date_format:H:i', 'after:start_time'],
            'session_type'     => ['required', Rule::in([
                Schedule::SESSION_REGULAR,
                Schedule::SESSION_LAB,
                Schedule::SESSION_EXAM,
                Schedule::SESSION_EXTRACURRICULAR,
                Schedule::SESSION_REMEDIAL,
            ])],
            'status'           => ['sometimes', Rule::in([
                Schedule::STATUS_ACTIVE,
                Schedule::STATUS_INACTIVE,
            ])],
        ]);

        $updated = $this->service->update($schedule, $data);

        return response()->json([
            'success' => true,
            'message' => 'Schedule berhasil diperbarui.',
            'data'    => $this->service->toResource($updated),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function destroy(int $id): JsonResponse
    {
        $schedule = $this->service->findOrFail($id);
        $this->service->delete($schedule);

        return response()->json([
            'success' => true,
            'message' => 'Schedule berhasil dihapus.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Status
    |--------------------------------------------------------------------------
    */

    public function toggleStatus(int $id): JsonResponse
    {
        $schedule = $this->service->findOrFail($id);
        $updated  = $this->service->toggleStatus($schedule);

        return response()->json([
            'success' => true,
            'message' => 'Status schedule berhasil diubah.',
            'status'  => $updated->status,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Dropdown data endpoints (consumed by the modal selects)
    |--------------------------------------------------------------------------
    */

    /**
     * GET /schedules/semesters
     * Returns all semesters for the dropdown.
     */
    public function semesters(): JsonResponse
    {
        $semesters = $this->semesterService->getActiveAcademicSemester([
                'per_page' => 'all',
                'sort_by'  => 'start_date',
                'sort_order' => 'desc',
            ]);

            return response()->json(
                $semesters->map(fn ($semester) => [
                    'semester_id'   => $semester->semester_id,
                    'semester_name' => $semester->semester_name,
                    'status'        => $semester->status,
                ])
            );
    }

    /**
     * GET /schedules/rooms
     * Returns all rooms for the dropdown.
     */
    public function rooms(): JsonResponse
    {
        $rooms = Room::orderBy('room_name')
            ->get(['room_id', 'room_name', 'code', 'type', 'capacity']);

        return response()->json($rooms);
    }

    /**
     * GET /schedules/grade-subjects
     * Returns grade_subjects with grade + subject + teacher for the dropdown.
     */
    public function gradeSubjects(): JsonResponse
    {
        $gradeSubjects = GradeSubject::with(['grade', 'subject', 'teacher'])
            ->where('status', 'active')
            ->get()
            ->map(fn ($gs) => [
                'id'           => $gs->id,
                'label'        => "{$gs->grade?->grade_name} — {$gs->subject?->subject_name}",
                'grade_name'   => $gs->grade?->grade_name,
                'subject_name' => $gs->subject?->subject_name,
                'teacher_name' => $gs->teacher?->full_name,
            ]);

        return response()->json($gradeSubjects);
    }
}