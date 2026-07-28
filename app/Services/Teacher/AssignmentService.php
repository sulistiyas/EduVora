<?php

namespace App\Services\Teacher;

use App\Concerns\HasSchoolScope;
use App\Models\Exam\Assigment;
use App\Models\Exam\AssigmentSubmission;
use App\Models\Student\Student;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssignmentService
{
    use HasSchoolScope;

    /**
     * Create a new assignment and initialize submissions for all students in the grade.
     *
     * @throws \Exception
     */
    public function createAssignment(array $data, int $teacherId): Assigment
    {
        return DB::transaction(function () use ($data, $teacherId) {
            $schoolId = $this->getAuthSchoolId();

            // Handle file upload
            if (isset($data['attachment']) && $data['attachment'] instanceof UploadedFile) {
                $path = $data['attachment']->store('assignments', 'public');
                $data['attachment'] = $path;
            }

            $assignment = Assigment::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'subject_id' => $data['subject_id'],
                'grade_id' => $data['grade_id'],
                'teacher_id' => $teacherId,
                'assigned_date' => $data['assigned_date'],
                'due_date' => $data['due_date'],
                'attachment' => $data['attachment'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'school_id' => $schoolId,
            ]);

            // Auto-create submissions for students in that grade
            $students = Student::where('school_id', $schoolId)
                ->where('grade_id', $data['grade_id'])
                ->get();

            $submissions = [];
            foreach ($students as $student) {
                $submissions[] = [
                    'assigment_id' => $assignment->id,
                    'student_id' => $student->id,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (! empty($submissions)) {
                AssigmentSubmission::insert($submissions);
            }

            return $assignment;
        });
    }

    /**
     * Update an existing assignment.
     */
    public function updateAssignment(Assigment $assignment, array $data): bool
    {
        if (isset($data['attachment']) && $data['attachment'] instanceof UploadedFile) {
            // Delete old file if exists
            if ($assignment->attachment) {
                Storage::disk('public')->delete($assignment->attachment);
            }
            $path = $data['attachment']->store('assignments', 'public');
            $data['attachment'] = $path;
        }

        return $assignment->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'assigned_date' => $data['assigned_date'],
            'due_date' => $data['due_date'],
            'status' => $data['status'],
            'attachment' => $data['attachment'] ?? $assignment->attachment,
        ]);
    }

    /**
     * Toggle assignment status.
     */
    public function toggleStatus(Assigment $assignment): bool
    {
        $newStatus = match ($assignment->status) {
            'draft' => 'published',
            'published' => 'closed',
            'closed' => 'draft',
            default => 'draft',
        };

        return $assignment->update(['status' => $newStatus]);
    }

    /**
     * Grade a student's submission.
     */
    public function gradeSubmission(int $submissionId, float $score, ?string $feedback): AssigmentSubmission
    {
        $submission = AssigmentSubmission::findOrFail($submissionId);

        $submission->update([
            'score' => $score,
            'feedback' => $feedback,
            'status' => 'graded',
        ]);

        return $submission;
    }

    /**
     * Delete an assignment.
     * Throws exception if there are graded submissions.
     *
     * @throws \Exception
     */
    public function deleteAssignment(Assigment $assignment): bool
    {
        if ($assignment->gradedCount() > 0) {
            throw new \Exception('Tidak dapat menghapus tugas yang sudah memiliki nilai.');
        }

        if ($assignment->attachment) {
            Storage::disk('public')->delete($assignment->attachment);
        }

        // Submissions will be cascade deleted by DB foreign key constraint,
        // but just in case we can delete associated files
        foreach ($assignment->submissions as $submission) {
            if ($submission->file_path) {
                Storage::disk('public')->delete($submission->file_path);
            }
        }

        return $assignment->delete();
    }
}
