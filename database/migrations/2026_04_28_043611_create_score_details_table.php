<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('score_details', function (Blueprint $table) {
            $table->id('score_detail_id');

            $table->foreignId('score_session_id')
                ->constrained('score_sessions', 'score_session_id')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->decimal('score', 5, 2)->nullable()->default(null);

            $table->decimal('max_score', 5, 2)
                ->default(100);

            $table->string('notes')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'score_session_id',
                'student_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_details');
    }
};
