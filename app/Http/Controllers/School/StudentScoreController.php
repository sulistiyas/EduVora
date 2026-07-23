<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Academic\GradeSubject;
use App\Models\Academic\Schedule;
use App\Services\Academic\ScheduleService;
use App\Services\School\StudentScoreService;
use App\Services\SemesterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentScoreController extends Controller
{
    public function __construct(
        protected StudentScoreService    $service,
        protected ScheduleService $scheduleService,
        protected SemesterService $semesterService,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX — list all score sessions for the logged-in teacher
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View|JsonResponse
    {
        $user = Auth::user();
        $isSchoolAdmin = $user->hasRole('school-admin');

        if ($isSchoolAdmin) {
            $schoolId = getAuthSchoolId();
        } else {
            $teacher = $user->teacher;
            abort_if(is_null($teacher), 403, 'Akun ini tidak terhubung ke data guru.');
        }

        if ($request->ajax() || $request->wantsJson()) {
            $sessions = $isSchoolAdmin
                ? $this->service->paginateBySchool(
                    schoolId: $schoolId,
                    filters:  $request->only([
                        'semester_id', 'grade_subject_id', 'score_type',
                        'is_published', 'search', 'date_from', 'date_to',
                    ]),
                    perPage: (int) $request->input('per_page', 15),
                )
                : $this->service->paginate(
                    teacherId: $teacher->teacher_id,
                    filters:   $request->only([
                        'semester_id', 'grade_subject_id', 'score_type',
                        'is_published', 'search', 'date_from', 'date_to',
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

        $activeSemester = $this->semesterService
            ->getActiveAcademicSemester(['per_page' => 'all'])
            ->first();

        $stats = $isSchoolAdmin
            ? $this->service->statsBySchool($schoolId)
            : $this->service->stats($teacher->teacher_id);

        $scheduleInfo = null;
        if ($request->filled('schedule_id') && ! $isSchoolAdmin) {
            $schedule = Schedule::with([
                'gradeSubject.subject',
                'gradeSubject.grade',
                'semester',
            ])->find((int) $request->schedule_id);

            if ($schedule && $schedule->gradeSubject?->teacher_id === $teacher->teacher_id) {
                $gs = $schedule->gradeSubject;
                $scheduleInfo = [
                    'schedule_id'      => $schedule->schedule_id,
                    'grade_subject_id' => $gs->id,
                    'semester_id'      => $schedule->semester_id,
                    'subject_name'     => $gs->subject?->subject_name,
                    'grade_name'       => $gs->grade?->grade_name,
                    'semester_name'    => $schedule->semester?->semester_name,
                    'label'            => ($gs->subject?->subject_name ?? '?') . ' — ' . ($gs->grade?->grade_name ?? '?'),
                ];
            }
        }

        return view('pages.teacher.score.index', compact('activeSemester', 'stats', 'scheduleInfo'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE — create new score session
    |
    | POST /teacher/scores
    |
    | Payload: { grade_subject_id, semester_id, score_type, title,
    |            description, score_date, max_score }
    |--------------------------------------------------------------------------
    */

    public function store(Request $request): JsonResponse
    {
        $teacher = Auth::user()->teacher;
        abort_if(is_null($teacher), 403, 'Akun ini tidak terhubung ke data guru.');

        $data = $request->validate([
            'grade_subject_id' => ['required', 'integer', 'exists:grade_subjects,id'],
            'semester_id'      => ['required', 'integer', 'exists:semesters,semester_id'],
            'score_type'       => ['required', 'in:daily,mid_exam,final_exam,assignment'],
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string', 'max:1000'],
            'score_date'       => ['required', 'date'],
            // 'max_score'        => ['required', 'numeric', 'min:1', 'max:1000'],
        ]);

        // Verify grade_subject belongs to this teacher
        $gradeSubject = GradeSubject::with('grade')
            ->where('id', $data['grade_subject_id'])
            ->where('teacher_id', $teacher->teacher_id)
            ->firstOrFail();

        $session = $this->service->createSession(
            data: [
                'grade_subject_id' => $data['grade_subject_id'],
                'semester_id'      => $data['semester_id'],
                'score_type'       => $data['score_type'],
                'title'            => $data['title'],
                'description'      => $data['description'] ?? null,
                'score_date'       => $data['score_date'],
                'is_published'     => false,
                // 'max_score'        => $data['max_score'],
            ],
            gradeId: $gradeSubject->grade_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Sesi nilai berhasil dibuat.',
            'data'    => $this->service->toDetailResource($session),
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW — detail form: isi/lihat nilai per siswa
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

        $initialData = $this->service->toDetailResource($session);

        return view('pages.teacher.score.show', compact('session', 'initialData'));
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE DETAILS — PATCH /teacher/scores/{id}/details
    |
    | Payload: { details: [{ student_id, score, max_score, notes }],
    |            title?, description?, score_date?, max_score? }
    |--------------------------------------------------------------------------
    */

    public function saveDetails(Request $request, int $id): JsonResponse
    {
        $session = $this->service->findOrFail($id);
        $teacher = Auth::user()->teacher;

        $this->service->authorizeTeacher($session, $teacher->teacher_id);

        $data = $request->validate([
            'details'                 => ['required', 'array', 'min:1'],
            'details.*.student_id'    => ['required', 'integer', 'exists:students,id'],
            'details.*.score'         => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'details.*.max_score'     => ['nullable', 'numeric', 'min:1', 'max:1000'],
            'details.*.notes'         => ['nullable', 'string', 'max:500'],
            // Optional session-level updates
            'title'                   => ['sometimes', 'string', 'max:255'],
            'description'             => ['sometimes', 'nullable', 'string', 'max:1000'],
            'score_date'              => ['sometimes', 'date'],
            'max_score'               => ['sometimes', 'numeric', 'min:1', 'max:1000'],
        ]);

        $sessionUpdate = collect(['title', 'description', 'score_date'])
            ->filter(fn ($k) => $request->has($k))
            ->mapWithKeys(fn ($k) => [$k => $data[$k] ?? null])
            ->toArray();

        $session = $this->service->saveDetails(
            $session,
            $data['details'],
            $sessionUpdate ?: null
        );

        return response()->json([
            'success' => true,
            'message' => 'Nilai berhasil disimpan.',
            'data'    => $this->service->toDetailResource($session),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TOGGLE PUBLISH — PATCH /teacher/scores/{id}/publish
    |--------------------------------------------------------------------------
    */

    public function togglePublish(int $id): JsonResponse
    {
        $session = $this->service->findOrFail($id);
        $teacher = Auth::user()->teacher;

        $this->service->authorizeTeacher($session, $teacher->teacher_id);

        $session = $this->service->togglePublish($session);
        $label   = $session->is_published ? 'dipublikasikan' : 'disembunyikan';

        return response()->json([
            'success' => true,
            'message' => "Nilai berhasil {$label}.",
            'data'    => $this->service->toResource($session),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE — PATCH /teacher/scores/{id}
    | Update session metadata only (title, description, score_type, score_date)
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, int $id): JsonResponse
    {
        $session = $this->service->findOrFail($id);
        $teacher = Auth::user()->teacher;

        $this->service->authorizeTeacher($session, $teacher->teacher_id);

        $data = $request->validate([
            'score_type'  => ['sometimes', 'in:daily,mid_exam,final_exam,assignment'],
            'title'       => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'score_date'  => ['sometimes', 'date'],
        ]);

        $session = $this->service->updateSession($session, $data);

        return response()->json([
            'success' => true,
            'message' => 'Sesi nilai berhasil diperbarui.',
            'data'    => $this->service->toResource($session),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY — DELETE /teacher/scores/{id}
    |--------------------------------------------------------------------------
    */

    public function destroy(int $id): JsonResponse
    {
        $session = $this->service->findOrFail($id);
        $teacher = Auth::user()->teacher;

        $this->service->authorizeTeacher($session, $teacher->teacher_id);

        // Cannot delete published sessions
        if ($session->is_published) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi yang sudah dipublikasikan tidak dapat dihapus.',
            ], 422);
        }

        $this->service->deleteSession($session);

        return response()->json([
            'success' => true,
            'message' => 'Sesi nilai berhasil dihapus.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DROPDOWN HELPERS
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

    /**
     * Get grade subjects (kelas-mapel) for this teacher.
     * Used to populate the "Buat Sesi" modal.
     */
    public function gradeSubjects(Request $request): JsonResponse
    {
        $user = Auth::user();
        $isSchoolAdmin = $user->hasRole('school-admin');

        if ($isSchoolAdmin) {
            $schoolId = getAuthSchoolId();
            $items = GradeSubject::with(['grade', 'subject', 'teacher'])
                ->whereHas('grade', fn ($q) => $q->where('school_id', $schoolId))
                ->get()
                ->map(fn ($gs) => [
                    'grade_subject_id' => $gs->id,
                    'subject_name'     => $gs->subject?->subject_name,
                    'grade_name'       => $gs->grade?->grade_name,
                    'teacher_name'     => $gs->teacher?->full_name,
                    'grade_id'         => $gs->grade_id,
                    'label'            => ($gs->subject?->subject_name ?? '?') . ' — ' . ($gs->grade?->grade_name ?? '?'),
                ]);
        } else {
            $teacher = $user->teacher;
            abort_if(is_null($teacher), 403);

            $items = GradeSubject::with(['grade', 'subject'])
                ->where('teacher_id', $teacher->teacher_id)
                ->get()
                ->map(fn ($gs) => [
                    'grade_subject_id' => $gs->id,
                    'subject_name'     => $gs->subject?->subject_name,
                    'grade_name'       => $gs->grade?->grade_name,
                    'grade_id'         => $gs->grade_id,
                    'label'            => ($gs->subject?->subject_name ?? '?') . ' — ' . ($gs->grade?->grade_name ?? '?'),
                ]);
        }

        return response()->json($items);
    }
}