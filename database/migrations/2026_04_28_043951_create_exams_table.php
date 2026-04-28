<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('exam_name');
            $table->bigInteger('subject_id')->unsigned()->nullable()->constrained('subjects')->onDelete('set null');
            $table->bigInteger('grade_id')->unsigned()->nullable()->constrained('grades')->onDelete('set null');
            $table->bigInteger('semester_id')->unsigned()->nullable()->constrained('semesters')->onDelete('set null');
            $table->enum('exam_type', ['uts', 'uas', 'quiz', 'remedial', 'placement', 'other'])->default('uts');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->decimal('total_score', 8, 2)->nullable();
            $table->text('instructions')->nullable();
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};