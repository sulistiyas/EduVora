<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->bigInteger('subject_id')->unsigned()->nullable()->constrained('subjects')->onDelete('set null');
            $table->bigInteger('teacher_id')->unsigned()->nullable()->constrained('teachers')->onDelete('set null');
            $table->enum('question_type', ['multiple_choice', 'essay', 'true_false', 'short_answer'])->default('multiple_choice');
            $table->text('content');
            $table->text('options')->nullable();
            $table->text('answer_key')->nullable();
            $table->string('difficulty')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_banks');
    }
};
