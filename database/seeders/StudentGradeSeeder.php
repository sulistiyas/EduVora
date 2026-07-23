<?php

namespace Database\Seeders;

use App\Models\Student\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentGradeSeeder extends Seeder
{
    public function run(): void
    {
        $schoolId = 1;

        $grades = DB::table('grades')
            ->where('school_id', $schoolId)
            ->orderBy('grade_id')
            ->get();

        $students = Student::query()
            ->whereNull('grade_id')
            ->whereHas('user.schools', function ($q) use ($schoolId) {
                $q->where('school_profiles.school_id', $schoolId);
            })
            ->orderBy('id')
            ->get();

        if ($grades->isEmpty()) {
            return;
        }

        $gradeCount = $grades->count();

        foreach ($students as $index => $student) {

            $grade = $grades[$index % $gradeCount];

            DB::table('students')
                ->where('id', $student->id)
                ->update([
                    'grade_id' => $grade->grade_id,
                    'updated_at' => now(),
                ]);
        }
    }
}
