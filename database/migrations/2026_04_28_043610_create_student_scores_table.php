<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_scores', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('student_id')->unsigned()->constrained('students')->onDelete('cascade');
            $table->bigInteger('grade_subject_id')->unsigned()->nullable()->constrained('grade_subjects')->onDelete('set null');
            $table->bigInteger('semester_id')->unsigned()->nullable()->constrained('semesters')->onDelete('set null');
            $table->bigInteger('subject_id')->unsigned()->nullable()->constrained('subjects')->onDelete('set null');
            $table->enum('score_type', ['daily', 'uts', 'uas', 'assignment', 'exam'])->default('daily');
            $table->decimal('score', 8, 2)->default(0);
            $table->tinyInteger('weight')->default(100);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_scores');
    }
};