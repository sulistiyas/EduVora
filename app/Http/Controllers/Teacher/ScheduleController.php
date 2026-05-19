<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Repositories\Academic\ScheduleRepository;
use App\Services\Academic\ScheduleService;
use App\Services\SemesterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function __construct(
        protected ScheduleService    $service,
        protected ScheduleRepository $repo,
        protected SemesterService    $semesterService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {
            $teacher   = Auth::user()->teacher; // sesuaikan relasi di User model
            $schedules = $this->repo->paginateByTeacher(
                teacherId: $teacher->teacher_id,
                filters:   $request->only(['semester_id', 'day_of_week', 'session_type']),
                perPage:   (int) $request->input('per_page', 10),
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

        return view('pages.teacher.schedules.index');
    }

    public function show(int $id): JsonResponse
    {
        $schedule = $this->service->findOrFail($id);

        // Pastikan jadwal ini memang milik teacher yang login
        $teacher = Auth::user()->teacher;
        abort_if(
            $schedule->gradeSubject?->teacher_id !== $teacher->teacher_id,
            403,
            'Anda tidak memiliki akses ke jadwal ini.'
        );

        return response()->json($this->service->toResource($schedule));
    }

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
}