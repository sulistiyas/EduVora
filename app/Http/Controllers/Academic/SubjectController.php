<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Services\Academic\SubjectsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class SubjectController extends Controller
{
    protected $subjectService;

    public function __construct(SubjectsService $subjectService)
    {
        $this->subjectService = $subjectService;
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

            $subjects = $this->subjectService->getAllSubjects($filters);

            if ($subjects instanceof LengthAwarePaginator) {
                return response()->json([
                    'data' => $subjects->items(),
                    'meta' => [
                        'current_page' => $subjects->currentPage(),
                        'per_page' => $subjects->perPage(),
                        'total' => $subjects->total(),
                        'last_page' => $subjects->lastPage(),
                    ],
                ]);
            }

            return response()->json([
                'data' => $subjects,
                'meta' => null,
            ]);
        }

        return view('pages.academic.subjects.index');
    }

    public function show($id)
    {
        return response()->json($this->subjectService->getSubjectById($id));
    }

    public function store(Request $request)
    {
        return response()->json($this->subjectService->createSubject($request->all()));
    }

    public function update(Request $request, $id)
    {
        return response()->json($this->subjectService->updateSubject($id, $request->all()));
    }

    public function toggleStatus($id): JsonResponse
    {
        $subjetcs = $this->subjectService->toggleStatus($id);

        if (!$subjetcs) {
            return response()->json(['success' => false, 'message' => 'Subject tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'status'  => $subjetcs->status,   // 'active' | 'inactive'
            'message' => $subjetcs->status === 'active' ? 'Subject diaktifkan.' : 'Subject dinonaktifkan.',
        ]);
    }

    public function destroy($id)
    {
        return response()->json(['success' => $this->subjectService->deleteSubject($id)]);
    }
}
