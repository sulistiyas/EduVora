<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('grade_id')->unsigned()->constrained('grades')->onDelete('cascade');
            $table->bigInteger('subject_id')->unsigned()->constrained('subjects')->onDelete('cascade');
            $table->bigInteger('teacher_id')->unsigned()->nullable()->constrained('teachers')->onDelete('set null');
            $table->bigInteger('room_id')->unsigned()->nullable()->constrained('rooms')->onDelete('set null');
            $table->bigInteger('semester_id')->unsigned()->nullable()->constrained('semesters')->onDelete('set null');
            $table->tinyInteger('day_of_week')->comment('1=Monday, 7=Sunday');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('session_type')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};