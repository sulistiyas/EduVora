<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Services\Academic\AcademicDateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicDateController extends Controller
{
    public function __construct(
        protected AcademicDateService $service
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $schoolId = getAuthSchoolId();

        if ($request->ajax() || $request->wantsJson()) {
            $dates = $this->service->paginate(
                schoolId: $schoolId,
                filters: $request->only(['semester_id', 'type', 'status']),
                perPage: (int) $request->input('per_page', 15),
            );

            return response()->json([
                'data' => $dates->map(fn ($d) => $this->service->toResource($d)),
                'meta' => [
                    'current_page' => $dates->currentPage(),
                    'per_page' => $dates->perPage(),
                    'total' => $dates->total(),
                    'last_page' => $dates->lastPage(),
                ],
            ]);
        }

        return view('pages.school-admin.academic-dates.index');
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'type' => ['required', 'in:holiday,exam,event,deadline,other'],
            'semester_id' => ['nullable', 'integer', 'exists:semesters,semester_id'],
        ]);

        $date = $this->service->create(getAuthSchoolId(), $data);

        return response()->json([
            'success' => true,
            'message' => 'Tanggal akademik berhasil ditambahkan.',
            'data' => $this->service->toResource($date),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $date = $this->service->findOrFail($id);
        abort_if($date->school_id !== getAuthSchoolId(), 403);

        return response()->json([
            'data' => $this->service->toResource($date),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $date = $this->service->findOrFail($id);
        abort_if($date->school_id !== getAuthSchoolId(), 403);

        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'type' => ['sometimes', 'required', 'in:holiday,exam,event,deadline,other'],
            'semester_id' => ['nullable', 'integer', 'exists:semesters,semester_id'],
            'status' => ['sometimes', 'in:active,inactive'],
        ]);

        $date = $this->service->update($date, $data);

        return response()->json([
            'success' => true,
            'message' => 'Tanggal akademik berhasil diperbarui.',
            'data' => $this->service->toResource($date),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $date = $this->service->findOrFail($id);
        abort_if($date->school_id !== getAuthSchoolId(), 403);

        $this->service->delete($date);

        return response()->json([
            'success' => true,
            'message' => 'Tanggal akademik berhasil dihapus.',
        ]);
    }
}
