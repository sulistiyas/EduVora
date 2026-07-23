<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extracurriculars_members', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('extracurricular_id')->unsigned()->constrained('extracurriculars')->onDelete('cascade');
            $table->bigInteger('student_id')->unsigned()->constrained('students')->onDelete('cascade');
            $table->date('join_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extracurriculars_members');
    }
};
