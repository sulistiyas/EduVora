<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Services\Academic\AttendanceService;
use App\Services\SemesterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $service,
        protected SemesterService $semesterService,
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $schoolId = getAuthSchoolId();

        if ($request->ajax() || $request->wantsJson()) {
            $sessions = $this->service->paginateBySchool(
                schoolId: $schoolId,
                filters: $request->only([
                    'semester_id', 'grade_id', 'subject_id',
                    'status', 'date_from', 'date_to',
                ]),
                perPage: (int) $request->input('per_page', 15),
            );

            return response()->json([
                'data' => $sessions->map(fn ($s) => $this->service->toResource($s)),
                'meta' => [
                    'current_page' => $sessions->currentPage(),
                    'per_page' => $sessions->perPage(),
                    'total' => $sessions->total(),
                    'last_page' => $sessions->lastPage(),
                ],
            ]);
        }

        $activeSemester = $this->semesterService
            ->getActiveAcademicSemester(['per_page' => 'all'])
            ->first();

        $stats = $this->service->statsBySchool($schoolId);

        return view('pages.school-admin.attendance.index', compact('activeSemester', 'stats'));
    }

    public function semesters(): JsonResponse
    {
        $semesters = $this->semesterService->getActiveAcademicSemester([
            'per_page' => 'all',
            'sort_by' => 'start_date',
            'sort_order' => 'desc',
        ]);

        return response()->json(
            $semesters->map(fn ($s) => [
                'semester_id' => $s->semester_id,
                'semester_name' => $s->semester_name,
                'status' => $s->status,
            ])
        );
    }
}
