<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('student_id')->unsigned()->constrained('students')->onDelete('cascade');
            $table->bigInteger('grade_id')->unsigned()->nullable()->constrained('grades')->onDelete('set null');
            $table->bigInteger('semester_id')->unsigned()->nullable()->constrained('semesters')->onDelete('set null');
            $table->decimal('gpa', 5, 2)->nullable();
            $table->decimal('final_score', 8, 2)->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_cards');
    }
};