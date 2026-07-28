<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam\Assigment;
use App\Models\Exam\AssigmentSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class StudentAssignmentController extends Controller
{
    private function getStudent()
    {
        return Auth::user()->student;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $student = $this->getStudent();

        if ($request->expectsJson()) {
            $query = Assigment::where('grade_id', $student->grade_id)
                ->where('school_id', $student->school_id)
                ->where('status', 'published')
                ->with(['subject', 'teacher', 'submissions' => function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                }]);

            if ($request->filled('search')) {
                $query->where('title', 'ILIKE', '%' . $request->search . '%');
            }

            if ($request->filled('status')) {
                // Filter by submission status
                $status = $request->status;
                $query->whereHas('submissions', function ($q) use ($student, $status) {
                    $q->where('student_id', $student->id)->where('status', $status);
                });
            }

            $assignments = $query->latest('assigned_date')->paginate(10);
            return response()->json($assignments);
        }

        return view('pages.student.assignment.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $student = $this->getStudent();
        
        $assignment = Assigment::where('id', $id)
            ->where('grade_id', $student->grade_id)
            ->where('school_id', $student->school_id)
            ->where('status', 'published')
            ->with(['subject', 'teacher'])
            ->firstOrFail();

        $submission = AssigmentSubmission::firstOrCreate([
            'assigment_id' => $assignment->id,
            'student_id' => $student->id,
        ], [
            'status' => 'pending'
        ]);

        return view('pages.student.assignment.show', compact('assignment', 'submission'));
    }

    /**
     * Submit an assignment.
     */
    public function submit(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,png,zip|max:10240',
            'feedback' => 'nullable|string', // optional message from student
        ]);

        $student = $this->getStudent();
        
        $assignment = Assigment::where('id', $id)
            ->where('grade_id', $student->grade_id)
            ->where('school_id', $student->school_id)
            ->where('status', 'published')
            ->first();

        if (!$assignment) {
            return response()->json(['message' => 'Tugas tidak ditemukan atau tidak tersedia.'], 404);
        }

        if ($assignment->isOverdue()) {
            return response()->json(['message' => 'Batas waktu pengumpulan tugas sudah terlewat.'], 400);
        }

        $submission = AssigmentSubmission::firstOrCreate([
            'assigment_id' => $assignment->id,
            'student_id' => $student->id,
        ], [
            'status' => 'pending'
        ]);

        if ($submission->status === 'graded') {
            return response()->json(['message' => 'Tugas sudah dinilai, tidak dapat diubah.'], 400);
        }

        if ($request->hasFile('attachment')) {
            // Delete old file if exists
            if ($submission->file_path) {
                Storage::disk('public')->delete($submission->file_path);
            }
            $path = $request->file('attachment')->store('submissions', 'public');
            $submission->file_path = $path;
        }

        $submission->status = 'submitted';
        $submission->submitted_at = now();
        $submission->save();

        return response()->json(['message' => 'Tugas berhasil dikumpulkan.']);
    }

    /**
     * Delete a submission (if not graded).
     */
    public function deleteSubmission(int $id): JsonResponse
    {
        $student = $this->getStudent();
        
        $submission = AssigmentSubmission::where('assigment_id', $id)
            ->where('student_id', $student->id)
            ->first();

        if (!$submission) {
            return response()->json(['message' => 'Submission tidak ditemukan.'], 404);
        }

        if ($submission->status === 'graded') {
            return response()->json(['message' => 'Tugas sudah dinilai, tidak dapat dibatalkan.'], 400);
        }

        if ($submission->file_path) {
            Storage::disk('public')->delete($submission->file_path);
            $submission->file_path = null;
        }

        $submission->status = 'pending';
        $submission->submitted_at = null;
        $submission->save();

        return response()->json(['message' => 'Pengumpulan tugas berhasil dibatalkan.']);
    }
}
