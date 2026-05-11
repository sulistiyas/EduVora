<?php

namespace App\Http\Controllers\Class;

use App\Http\Controllers\Controller;
use App\Services\GradesService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradesController extends Controller
{
    protected GradesService $gradeService;

    public function __construct(GradesService $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    // ─── Dropdown Endpoints ────────────────────────────────────────────────────

    public function getRooms(): JsonResponse
    {
        return response()->json(['data' => $this->gradeService->getRoomsForDropdown()]);
    }

    public function getTeachers(): JsonResponse
    {
        return response()->json(['data' => $this->gradeService->getTeachersForDropdown()]);
    }

    public function getAcademicYears(): JsonResponse
    {
        return response()->json(['data' => $this->gradeService->getAcademicYearsForDropdown()]);
    }

    public function detail(Request $request, $id): View|JsonResponse
    {
        $grade = $this->gradeService->getGradeById($id);
    
        if (!$grade) {
            abort(404, 'Kelas tidak ditemukan.');
        }
    
        if ($request->expectsJson()) {
            return response()->json($grade);
        }
    
        return view('pages.schools.class.grades.detail', [
            'grade' => $grade,
        ]);
    }
    
    /**
     * Dropdown subjects
     */
    public function getSubjects(): JsonResponse
    {
        return response()->json(['data' => $this->gradeService->getSubjectsForDropdown()]);
    }

    // ─── CRUD ──────────────────────────────────────────────────────────────────

    public function index(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {
            $filters = [
                'search'           => $request->query('search'),
                'status'           => $request->query('status'),
                'room_id'          => $request->query('room_id'),
                'academic_year_id' => $request->query('academic_year_id'),
                'sort_by'          => $request->query('sort_by'),
                'sort_order'       => $request->query('sort_order'),
                'per_page'         => $request->query('per_page', 10),
            ];

            $grades = $this->gradeService->getAllGrades($filters);

            if ($grades instanceof LengthAwarePaginator) {
                return response()->json([
                    'data' => $grades->items(),
                    'meta' => [
                        'current_page' => $grades->currentPage(),
                        'per_page'     => $grades->perPage(),
                        'total'        => $grades->total(),
                        'last_page'    => $grades->lastPage(),
                    ],
                ]);
            }

            return response()->json(['data' => $grades, 'meta' => null]);
        }

        return view('pages.schools.class.grades.index');
    }

    public function show($id): JsonResponse
    {
        $grade = $this->gradeService->getGradeById($id);

        if (!$grade) {
            return response()->json(['message' => 'Kelas tidak ditemukan.'], 404);
        }

        return response()->json($grade);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $grade = $this->gradeService->createGrade($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Kelas berhasil ditambahkan.',
                'data'    => $grade,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $grade = $this->gradeService->updateGrade($id, $request->all());

        if (!$grade) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil diperbarui.',
            'data'    => $grade,
        ]);
    }

    public function toggleStatus($id): JsonResponse
    {
        try {
            $grade = $this->gradeService->toggleStatus($id);

            if (!$grade) {
                return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan.'], 404);
            }

            $messages = [
                'active'   => 'Kelas diaktifkan.',
                'inactive' => 'Kelas dinonaktifkan.',
                'graduated'=> 'Kelas ditandai lulus.',
                'archived' => 'Kelas diarsipkan.',
            ];

            return response()->json([
                'success' => true,
                'status'  => $grade->status,
                'message' => $messages[$grade->status] ?? 'Status diperbarui.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        }
    }

    public function destroy($id): JsonResponse
    {
        $deleted = $this->gradeService->deleteGrade($id);

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil dihapus.',
        ]);
    }
}