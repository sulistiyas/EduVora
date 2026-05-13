<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {

            $table->id('schedule_id');

            /**
             * School
             */
            $table->foreignId('school_id')
                ->constrained('school_profiles', 'school_id')
                ->cascadeOnDelete();

            /**
             * Grade Subject
             */
            $table->foreignId('grade_subject_id')
                ->constrained('grade_subjects')
                ->cascadeOnDelete();

            /**
             * Room
             */
            $table->foreignId('room_id')
                ->nullable()
                ->constrained('rooms', 'room_id')
                ->nullOnDelete();

            /**
             * Semester
             */
            $table->foreignId('semester_id')
                ->nullable()
                ->constrained('semesters', 'semester_id')
                ->nullOnDelete();

            /**
             * Day
             * 1 = Monday
             * 2 = Tuesday
             * 3 = Wednesday
             * 4 = Thursday
             * 5 = Friday
             * 6 = Saturday
             * 7 = Sunday
             */
            $table->tinyInteger('day_of_week');

            /**
             * Time
             */
            $table->time('start_time');
            $table->time('end_time');

            /**
             * Session Type
             */
            $table->enum('session_type', [
                'regular',
                'lab',
                'exam',
                'extracurricular',
                'remedial',
            ])->default('regular');

            /**
             * Status
             */
            $table->enum('status', [
                'active',
                'inactive',
            ])->default('active');

            $table->timestamps();

            /**
             * Prevent duplicate schedule
             */
            $table->unique([
                'school_id',
                'room_id',
                'semester_id',
                'day_of_week',
                'start_time',
            ], 'unique_room_schedule');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};