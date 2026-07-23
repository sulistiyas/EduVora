<?php

namespace App\Http\Controllers\Academic;

use App\Http\Requests\StoreAcademicYearRequest;
use App\Http\Requests\UpdateAcademicYearRequest;
use App\Http\Controllers\Controller;
use App\Services\AcademicYearService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class AcademicYearController extends Controller
{
    protected $academicYearService;

    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {

            $filters = [
                'search' => $request->query('search'),
                'status' => $request->query('status'),
                'sort_by' => $request->query('sort_by'),
                'sort_order' => $request->query('sort_order'),
                'per_page' => $request->query('per_page', 10),
            ];

            $academicYears = $this->academicYearService->getAllAcademicYears($filters);

            if ($academicYears instanceof LengthAwarePaginator) {
                return response()->json([
                    'data' => $academicYears->items(),
                    'meta' => [
                        'current_page' => $academicYears->currentPage(),
                        'per_page' => $academicYears->perPage(),
                        'total' => $academicYears->total(),
                        'last_page' => $academicYears->lastPage(),
                    ],
                ]);
            }

            return response()->json([
                'data' => $academicYears,
                'meta' => null,
            ]);
        }

        return view('pages.academic.academicYear.index');
    }

    public function show($id)
    {
        return response()->json($this->academicYearService->getAcademicYearById($id));
    }

    public function store(StoreAcademicYearRequest $request)
    {
        return response()->json($this->academicYearService->createAcademicYear($request->validated()));
    }

    public function update(UpdateAcademicYearRequest $request, $id)
    {
        return response()->json($this->academicYearService->updateAcademicYear($id, $request->validated()));
    }

    public function toggleStatus($id): JsonResponse
    {
        $academicYears = $this->academicYearService->toggleStatus($id);

        if (!$academicYears) {
            return response()->json(['success' => false, 'message' => 'Academic Year tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'status'  => $academicYears->status,   // 'active' | 'inactive'
            'message' => $academicYears->status === 'active' ? 'Academic Year diaktifkan.' : 'Academic Year dinonaktifkan.',
        ]);
    }

    public function destroy($id)
    {
        return response()->json(['success' => $this->academicYearService->deleteAcademicYear($id)]);
    }
}
