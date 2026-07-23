<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assigment_submissions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('assigment_id')->unsigned()->constrained('assigments')->onDelete('cascade');
            $table->bigInteger('student_id')->unsigned()->constrained('students')->onDelete('cascade');
            $table->dateTime('submitted_at')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['pending', 'submitted', 'graded'])->default('pending');
            $table->decimal('score', 8, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assigment_submissions');
    }
};
