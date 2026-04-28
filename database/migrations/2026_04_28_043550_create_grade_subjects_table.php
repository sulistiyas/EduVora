<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_subjects', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('grade_id')->unsigned()->constrained('grades')->onDelete('cascade');
            $table->bigInteger('subject_id')->unsigned()->constrained('subjects')->onDelete('cascade');
            $table->bigInteger('teacher_id')->unsigned()->nullable()->constrained('teachers')->onDelete('set null');
            $table->tinyInteger('kkm')->nullable();
            $table->tinyInteger('weight_harian')->default(30);
            $table->tinyInteger('weight_uts')->default(30);
            $table->tinyInteger('weight_uas')->default(40);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_subjects');
    }
};