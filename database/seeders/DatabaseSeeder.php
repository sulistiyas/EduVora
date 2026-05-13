<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('TRUNCATE roles,users,user_has_roles,school_profiles,user_has_schools,students,student_parents,teachers, academic_years, semesters, rooms, subjects, grades, grade_subjects, schedules RESTART IDENTITY CASCADE');
        $this->call([
            RoleSeeder::class,
            SchoolSeeder::class,
            UserSeeder::class,
            StudentSeeder::class,
            TeacherSeeder::class,
            StudentParentSeeder::class,
            AcademicYearSeeder::class,
            RoomSeeder::class,
            GradeSeeder::class,
            SubjectSeeder::class,
            GradeSubjectSeeder::class,
            ScheduleSeeder::class
        ]);
    }
}
