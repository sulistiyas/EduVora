<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teaching_journals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('teacher_id')->unsigned()->nullable()->constrained('teachers')->onDelete('set null');
            $table->bigInteger('grade_subject_id')->unsigned()->nullable()->constrained('grade_subjects')->onDelete('set null');
            $table->date('lesson_date')->nullable();
            $table->string('topic')->nullable();
            $table->text('material_covered')->nullable();
            $table->text('activities')->nullable();
            $table->text('reflection')->nullable();
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teaching_journals');
    }
};
