<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id('grade_id');

            $table->unsignedBigInteger('school_id');

            $table->foreign('school_id')
                ->references('school_id')
                ->on('school_profiles')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('academic_year_id');

            $table->unsignedBigInteger('room_id')->nullable();

            $table->unsignedBigInteger('homeroom_teacher_id')->nullable();

            $table->foreign('academic_year_id')
                ->references('academic_year_id')
                ->on('academic_years')
                ->cascadeOnDelete();

            $table->foreign('room_id')
                ->references('room_id')
                ->on('rooms')
                ->nullOnDelete();

            $table->foreign('homeroom_teacher_id')
                ->references('teacher_id')
                ->on('teachers')
                ->nullOnDelete();

            $table->string('grade_name');

            $table->unsignedTinyInteger('level');

            $table->enum('status', [
                'active',
                'inactive',
                'graduated',
                'archived'
            ])->default('active');

            $table->timestamps();

            $table->softDeletes();

            $table->unique(['school_id', 'grade_name']);

            $table->index('school_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};