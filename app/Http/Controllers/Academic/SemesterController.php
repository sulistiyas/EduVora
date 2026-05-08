<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Academic\AcademicYear;
use App\Services\SemesterService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SemesterController extends Controller
{
    protected $semesterService;

    public function __construct(SemesterService $semesterService)
    {
        $this->semesterService = $semesterService;
    }

    public function getAcademicYears(): JsonResponse
    {
        $schoolId = Auth::user()->schools->first()->school_id ?? null;

        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->select(['academic_year_id', 'academic_year_name', 'status'])
            ->orderBy('academic_year_name', 'desc')
            ->get();

        return response()->json(['data' => $academicYears]);
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {

            $filters = [
                'search'           => $request->query('search'),
                'status'           => $request->query('status'),
                'academic_year_id' => $request->query('academic_year_id'),
                'sort_by'          => $request->query('sort_by'),
                'sort_order'       => $request->query('sort_order'),
                'per_page'         => $request->query('per_page', 10),
            ];

            $semesters = $this->semesterService->getAllSemesters($filters);

            if ($semesters instanceof LengthAwarePaginator) {
                return response()->json([
                    'data' => $semesters->items(),
                    'meta' => [
                        'current_page' => $semesters->currentPage(),
                        'per_page'     => $semesters->perPage(),
                        'total'        => $semesters->total(),
                        'last_page'    => $semesters->lastPage(),
                    ],
                ]);
            }

            return response()->json([
                'data' => $semesters,
                'meta' => null,
            ]);
        }

        return view('pages.academic.semester.index');
    }

    public function show($id): JsonResponse
    {
        $semester = $this->semesterService->getSemesterById($id);

        if (!$semester) {
            return response()->json(['message' => 'Semester tidak ditemukan.'], 404);
        }

        return response()->json($semester);
    }

    public function store(Request $request): JsonResponse
    {
        $semester = $this->semesterService->createSemester($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Semester berhasil ditambahkan.',
            'data'    => $semester,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $semester = $this->semesterService->updateSemester($id, $request->all());

        if (!$semester) {
            return response()->json(['success' => false, 'message' => 'Semester tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Semester berhasil diperbarui.',
            'data'    => $semester,
        ]);
    }

    public function toggleStatus($id): JsonResponse
    {
        $semester = $this->semesterService->toggleStatus($id);

        if (!$semester) {
            return response()->json(['success' => false, 'message' => 'Semester tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'status'  => $semester->status,
            'message' => $semester->status === 'active'
                ? 'Semester diaktifkan.'
                : 'Semester dinonaktifkan.',
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $deleted = $this->semesterService->deleteSemester($id);

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Semester tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Semester berhasil dihapus.',
        ]);
    }
}