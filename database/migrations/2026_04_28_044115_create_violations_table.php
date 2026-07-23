<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('violations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('student_id')->unsigned()->constrained('students')->onDelete('cascade');
            $table->bigInteger('violation_type_id')->unsigned()->nullable()->constrained('violation_types')->onDelete('set null');
            $table->bigInteger('reported_by')->unsigned()->nullable()->constrained('users')->onDelete('set null');
            $table->text('description')->nullable();
            $table->date('violation_date')->nullable();
            $table->enum('status', ['pending', 'investigated', 'resolved'])->default('pending');
            $table->text('resolution')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('violations');
    }
};
