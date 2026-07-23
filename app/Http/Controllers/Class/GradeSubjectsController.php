<?php

namespace App\Http\Controllers\Class;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGradeSubjectRequest;
use App\Http\Requests\UpdateGradeSubjectRequest;
use App\Services\Academic\GradeSubjectsService;
use Illuminate\Http\JsonResponse;

class GradeSubjectsController extends Controller
{
    public function __construct(protected GradeSubjectsService $service) {}

    /**
     * GET /grades/{gradeId}/subjects
     * Daftar mapel yang di-assign ke kelas ini
     */
    public function index($gradeId): JsonResponse
    {
        $items = $this->service->getByGrade($gradeId);

        return response()->json(['data' => $items]);
    }

    /**
     * POST /grades/{gradeId}/subjects
     * Assign mapel baru ke kelas
     */
    public function store(StoreGradeSubjectRequest $request, $gradeId): JsonResponse
    {
        try {
            $item = $this->service->assignSubject($gradeId, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Mata pelajaran berhasil ditambahkan ke kelas.',
                'data' => $item,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * PATCH /grades/{gradeId}/subjects/{id}
     * Update teacher / KKM / bobot untuk 1 baris grade_subject
     */
    public function update(UpdateGradeSubjectRequest $request, $gradeId, $id): JsonResponse
    {
        try {
            $item = $this->service->updateSubject($gradeId, $id, $request->validated());
            if (! $item) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui.',
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * DELETE /grades/{gradeId}/subjects/{id}
     * Hapus mapel dari kelas
     */
    public function destroy($gradeId, $id): JsonResponse
    {
        $deleted = $this->service->removeSubject($gradeId, $id);
        if (! $deleted) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Mata pelajaran berhasil dihapus dari kelas.']);
    }
}
