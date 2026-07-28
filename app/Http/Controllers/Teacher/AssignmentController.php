<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use App\Models\Academic\GradeSubject;
use App\Models\Exam\Assigment;
use App\Models\Exam\AssigmentSubmission;
use App\Repositories\Teacher\AssignmentRepository;
use App\Services\Teacher\AssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(AssignmentRepository $repository, AssignmentService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    private function getTeacherId()
    {
        return Auth::user()->teacher->teacher_id;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $teacherId = $this->getTeacherId();
        
        if ($request->expectsJson()) {
            $filters = $request->only(['status', 'search', 'date_from', 'date_to']);
            $assignments = $this->repository->getTeacherAssignments($teacherId, $filters);
            return response()->json($assignments);
        }

        $stats = $this->repository->getTeacherAssignmentStats($teacherId);

        return view('pages.teacher.assignment.index', compact('stats'));
    }

    /**
     * Get dropdown data for grade and subjects.
     */
    public function gradeSubjects(Request $request): JsonResponse
    {
        $teacherId = $this->getTeacherId();
        
        $gradeSubjects = GradeSubject::with(['grade', 'subject'])
            ->where('teacher_id', $teacherId)
            ->get()
            ->map(function ($gs) {
                return [
                    'grade_id' => $gs->grade_id,
                    'subject_id' => $gs->subject_id,
                    'label' => $gs->grade->grade_name . ' - ' . $gs->subject->subject_name,
                ];
            });
            
        return response()->json($gradeSubjects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssignmentRequest $request): JsonResponse
    {
        $teacherId = $this->getTeacherId();

        // Verify that the teacher teaches this subject in this grade
        $validGradeSubject = GradeSubject::where('teacher_id', $teacherId)
            ->where('grade_id', $request->grade_id)
            ->where('subject_id', $request->subject_id)
            ->exists();

        if (!$validGradeSubject) {
            return response()->json(['message' => 'Anda tidak mengajar mata pelajaran ini di kelas tersebut.'], 403);
        }

        try {
            $assignment = $this->service->createAssignment($request->validated(), $teacherId);
            return response()->json([
                'message' => 'Tugas berhasil dibuat.',
                'data' => $assignment
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal membuat tugas: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $teacherId = $this->getTeacherId();
        $assignment = $this->repository->getAssignmentByIdAndTeacher($id, $teacherId);

        if (!$assignment) {
            abort(404, 'Tugas tidak ditemukan atau Anda tidak memiliki akses.');
        }

        return view('pages.teacher.assignment.show', compact('assignment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssignmentRequest $request, int $id): JsonResponse
    {
        $teacherId = $this->getTeacherId();
        $assignment = $this->repository->getAssignmentByIdAndTeacher($id, $teacherId);

        if (!$assignment) {
            return response()->json(['message' => 'Tugas tidak ditemukan.'], 404);
        }

        try {
            $this->service->updateAssignment($assignment, $request->validated());
            return response()->json(['message' => 'Tugas berhasil diperbarui.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui tugas: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Toggle the status of the assignment.
     */
    public function toggleStatus(int $id): JsonResponse
    {
        $teacherId = $this->getTeacherId();
        $assignment = $this->repository->getAssignmentByIdAndTeacher($id, $teacherId);

        if (!$assignment) {
            return response()->json(['message' => 'Tugas tidak ditemukan.'], 404);
        }

        $this->service->toggleStatus($assignment);

        return response()->json(['message' => 'Status tugas berhasil diubah.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $teacherId = $this->getTeacherId();
        $assignment = $this->repository->getAssignmentByIdAndTeacher($id, $teacherId);

        if (!$assignment) {
            return response()->json(['message' => 'Tugas tidak ditemukan.'], 404);
        }

        try {
            $this->service->deleteAssignment($assignment);
            return response()->json(['message' => 'Tugas berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Grade a student's submission.
     */
    public function gradeSubmission(Request $request, int $id, int $submissionId): JsonResponse
    {
        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $teacherId = $this->getTeacherId();
        $assignment = $this->repository->getAssignmentByIdAndTeacher($id, $teacherId);

        if (!$assignment) {
            return response()->json(['message' => 'Tugas tidak ditemukan.'], 404);
        }

        // Verify submission belongs to this assignment
        $submissionExists = $assignment->submissions()->where('id', $submissionId)->exists();
        if (!$submissionExists) {
            return response()->json(['message' => 'Submission tidak ditemukan pada tugas ini.'], 404);
        }

        try {
            $this->service->gradeSubmission($submissionId, $request->score, $request->feedback);
            return response()->json(['message' => 'Nilai berhasil disimpan.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyimpan nilai: ' . $e->getMessage()], 500);
        }
    }
}
