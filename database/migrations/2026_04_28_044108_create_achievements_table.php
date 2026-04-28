<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('student_id')->unsigned()->nullable()->constrained('students')->onDelete('set null');
            $table->bigInteger('extracurricular_id')->unsigned()->nullable()->constrained('extracurriculars')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('level')->nullable();
            $table->date('achievement_date')->nullable();
            $table->string('certificate')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};