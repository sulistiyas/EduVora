<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_subjects', function (Blueprint $table) {

            $table->id();

            $table->foreignId('grade_id')
                ->constrained('grades', 'grade_id')
                ->cascadeOnDelete();

            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->cascadeOnDelete();

            $table->foreignId('teacher_id')
                ->nullable()
                ->constrained('teachers', 'teacher_id')
                ->nullOnDelete();

            $table->tinyInteger('kkm')->nullable();

            $table->tinyInteger('weight_harian')->default(30);
            $table->tinyInteger('weight_uts')->default(30);
            $table->tinyInteger('weight_uas')->default(40);

            $table->enum('status', [
                'active',
                'inactive'
            ])->default('active');

            $table->timestamps();

            $table->unique(['grade_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_subjects');
    }
};