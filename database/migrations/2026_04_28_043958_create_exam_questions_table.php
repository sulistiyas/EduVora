<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('exam_id')->unsigned()->constrained('exams')->onDelete('cascade');
            $table->bigInteger('question_bank_id')->unsigned()->nullable()->constrained('question_banks')->onDelete('set null');
            $table->decimal('point_value', 8, 2)->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
    }
};