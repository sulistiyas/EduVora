<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('student_id')->unsigned()->constrained('students')->onDelete('cascade');
            $table->bigInteger('schedule_id')->unsigned()->nullable()->constrained('schedules')->onDelete('set null');
            $table->bigInteger('subject_id')->unsigned()->nullable()->constrained('subjects')->onDelete('set null');
            $table->bigInteger('teacher_id')->unsigned()->nullable()->constrained('teachers')->onDelete('set null');
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'sick', 'permission', 'late'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};