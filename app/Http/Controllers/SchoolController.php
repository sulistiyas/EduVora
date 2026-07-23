<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolRequest;
use App\Http\Requests\UpdateSchoolRequest;
use App\Services\SchoolService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolController extends Controller
{
    protected $schoolService;

    public function __construct(SchoolService $schoolService)
    {
        $this->schoolService = $schoolService;
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

            $schools = $this->schoolService->getAllSchools($filters);

            // ✅ Handle paginator vs collection
            if ($schools instanceof LengthAwarePaginator) {
                return response()->json([
                    'data' => $schools->items(),
                    'meta' => [
                        'current_page' => $schools->currentPage(),
                        'per_page' => $schools->perPage(),
                        'total' => $schools->total(),
                        'last_page' => $schools->lastPage(),
                    ],
                ]);
            }

            return response()->json([
                'data' => $schools,
                'meta' => null,
            ]);
        }

        return view('pages.schools.index');
    }

    public function show($id)
    {
        return response()->json($this->schoolService->getSchoolById($id));
    }

    public function detail($id)
    {
        $school = $this->schoolService->getSchoolById($id);

        if (! $school) {
            abort(404, 'Sekolah tidak ditemukan.');
        }

        return view('pages.schools.detail', compact('school'));
    }

    public function store(StoreSchoolRequest $request)
    {
        return response()->json($this->schoolService->createSchool($request->validated()));
    }

    public function update(UpdateSchoolRequest $request, $id)
    {
        return response()->json($this->schoolService->updateSchool($id, $request->validated()));
    }

    public function toggleStatus($id): JsonResponse
    {
        $school = $this->schoolService->toggleStatus($id);

        if (! $school) {
            return response()->json(['success' => false, 'message' => 'Sekolah tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'status' => $school->status,   // 'active' | 'inactive'
            'message' => $school->status === 'active' ? 'Sekolah diaktifkan.' : 'Sekolah dinonaktifkan.',
        ]);
    }

    public function destroy($id)
    {
        return response()->json(['success' => $this->schoolService->deleteSchool($id)]);
    }
}
