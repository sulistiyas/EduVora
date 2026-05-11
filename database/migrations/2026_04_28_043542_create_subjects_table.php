<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->foreign('school_id')
                ->references('school_id')
                ->on('school_profiles')
                ->cascadeOnDelete();
            $table->string('subject_name');
            $table->string('subject_code')->unique();
            $table->string('category')->nullable();
            $table->tinyInteger('credits')->nullable();
            $table->tinyInteger('hours_per_week')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};