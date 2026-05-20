<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('score_sessions', function (Blueprint $table) {
            $table->id('score_session_id');

            $table->foreignId('grade_subject_id')
                ->constrained('grade_subjects')
                ->cascadeOnDelete();

            $table->foreignId('semester_id')
                ->nullable()
                ->constrained('semesters', 'semester_id')
                ->nullOnDelete();

            $table->enum('score_type', [
                'daily',
                'assignment',
                'mid_exam',
                'final_exam'
            ]);

            $table->string('title');

            $table->text('description')->nullable();

            $table->date('score_date');

            $table->boolean('is_published')
                ->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_sessions');
    }
};