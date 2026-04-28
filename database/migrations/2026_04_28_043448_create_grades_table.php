<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id('grade_id');
            $table->bigInteger('academic_year_id')->unsigned();
            $table->bigInteger('room_id')->unsigned()->nullable();
            $table->bigInteger('homeroom_teacher_id')->unsigned()->nullable();
            $table->foreign('academic_year_id')->references('academic_year_id')->on('academic_years')->onDelete('cascade');
            $table->foreign('room_id')->references('room_id')->on('rooms')->onDelete('set null');
            $table->foreign('homeroom_teacher_id')->references('teacher_id')->on('teachers')->onDelete('set null');
            $table->string('grade_name')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};