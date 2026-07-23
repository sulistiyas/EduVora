<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_sessions', function (Blueprint $table) {

            $table->id('attendance_session_id');

            /**
             * MULTI SCHOOL / TENANT
             */
            $table->foreignId('school_id')
                ->constrained('school_profiles', 'school_id')
                ->cascadeOnDelete();

            /**
             * RELATIONS
             */
            $table->foreignId('schedule_id')
                ->constrained('schedules', 'schedule_id')
                ->cascadeOnDelete();

            $table->foreignId('teacher_id')
                ->constrained('teachers', 'teacher_id')
                ->cascadeOnDelete();

            $table->foreignId('subject_id')
                ->constrained('subjects', 'id')
                ->cascadeOnDelete();

            $table->foreignId('grade_id')
                ->constrained('grades', 'grade_id')
                ->cascadeOnDelete();

            $table->foreignId('semester_id')
                ->constrained('semesters', 'semester_id')
                ->cascadeOnDelete();

            /**
             * ATTENDANCE INFO
             */
            $table->date('attendance_date');

            // Pertemuan ke-
            $table->unsignedTinyInteger('meeting_number')
                ->nullable();

            /**
             * SESSION STATUS
             *
             * draft     = belum submit
             * submitted = sudah submit guru
             * approved  = diverifikasi admin/wali kelas
             */
            $table->enum('status', [
                'draft',
                'submitted',
                'approved',
            ])->default('draft');

            /**
             * LOCK SESSION
             *
             * Jika true maka attendance
             * tidak bisa diedit lagi
             */
            $table->boolean('is_locked')
                ->default(false);

            /**
             * NOTES
             */
            $table->text('notes')
                ->nullable();

            /**
             * USER YANG INPUT
             */
            $table->foreignId('recorded_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            /**
             * PREVENT DUPLICATE SESSION
             *
             * 1 schedule hanya boleh
             * punya 1 attendance per hari
             */
            $table->unique([
                'schedule_id',
                'attendance_date',
            ], 'unique_schedule_attendance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');
    }
};
