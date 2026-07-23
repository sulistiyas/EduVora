<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_dates', function (Blueprint $table) {
            $table->id('academic_date_id');
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('semester_id')->nullable();

            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('type', ['holiday', 'exam', 'event', 'deadline', 'other'])->default('other');
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')
                ->references('school_id')
                ->on('school_profiles')
                ->cascadeOnDelete();

            $table->foreign('semester_id')
                ->references('semester_id')
                ->on('semesters')
                ->nullOnDelete();

            $table->index('school_id');
            $table->index(['school_id', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_dates');
    }
};
