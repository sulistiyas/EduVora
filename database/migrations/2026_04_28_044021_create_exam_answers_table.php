<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('exam_session_id')->unsigned()->constrained('exam_sessions')->onDelete('cascade');
            $table->bigInteger('exam_question_id')->unsigned()->constrained('exam_questions')->onDelete('cascade');
            $table->text('answer_text')->nullable();
            $table->decimal('score', 8, 2)->nullable();
            $table->boolean('is_correct')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};
