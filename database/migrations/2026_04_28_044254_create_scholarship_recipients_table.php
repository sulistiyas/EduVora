<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarship_recipients', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('scholarship_id')->unsigned()->constrained('scholarships')->onDelete('cascade');
            $table->bigInteger('student_id')->unsigned()->constrained('students')->onDelete('cascade');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'suspended', 'completed'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarship_recipients');
    }
};
