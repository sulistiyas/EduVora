<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counselling_sessions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('student_id')->unsigned()->constrained('students')->onDelete('cascade');
            $table->bigInteger('counselor_id')->unsigned()->nullable()->constrained('teachers')->onDelete('set null');
            $table->date('session_date')->nullable();
            $table->text('topic')->nullable();
            $table->text('notes')->nullable();
            $table->text('follow_up')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counselling_sessions');
    }
};