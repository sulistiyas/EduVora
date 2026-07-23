<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('student_id')->unsigned()->nullable()->constrained('students')->onDelete('set null');
            $table->date('registration_date')->nullable();
            $table->enum('registration_type', ['new', 'transfer', 're_registration'])->default('new');
            $table->bigInteger('grade_id')->unsigned()->nullable()->constrained('grades')->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
