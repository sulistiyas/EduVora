<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assigments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->bigInteger('subject_id')->unsigned()->nullable()->constrained('subjects')->onDelete('set null');
            $table->bigInteger('grade_id')->unsigned()->nullable()->constrained('grades')->onDelete('set null');
            $table->bigInteger('teacher_id')->unsigned()->nullable()->constrained('teachers')->onDelete('set null');
            $table->date('assigned_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('attachment')->nullable();
            $table->enum('status', ['draft', 'published', 'closed'])->default('published');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assigments');
    }
};