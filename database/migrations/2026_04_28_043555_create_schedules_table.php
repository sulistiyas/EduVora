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

        $table->foreignId('grade_subject_id')
            ->constrained('grade_subjects')
            ->cascadeOnDelete();

        $table->foreignId('room_id')
            ->nullable()
            ->constrained('rooms', 'room_id')
            ->nullOnDelete();

        $table->foreignId('semester_id')
            ->nullable()
            ->constrained('semesters', 'semester_id')
            ->nullOnDelete();

        $table->tinyInteger('day_of_week');

        $table->time('start_time');

        $table->time('end_time');

        $table->string('session_type')->nullable();

        $table->enum('status', [
            'active',
            'inactive'
        ])->default('active');

        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};