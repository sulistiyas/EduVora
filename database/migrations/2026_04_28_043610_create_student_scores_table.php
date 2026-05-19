<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_scores', function (Blueprint $table) {
            $table->id('student_score_id');

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('grade_subject_id')
                ->nullable()
                ->constrained('grade_subjects')
                ->nullOnDelete();

            $table->foreignId('semester_id')
                ->nullable()
                ->constrained('semesters','semester_id')
                ->nullOnDelete();

            $table->enum('score_type', [
                'daily',
                'mid_exam',
                'final_exam',
                'assignment'
            ]);

            $table->decimal('score', 5, 2)->default(0);
            $table->decimal('max_score', 5, 2)->default(100);

            $table->string('description')->nullable();

            $table->date('date');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_scores');
    }
};