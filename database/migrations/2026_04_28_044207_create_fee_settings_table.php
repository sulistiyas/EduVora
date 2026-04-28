<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fee_type_id')->unsigned()->constrained('fee_types')->onDelete('cascade');
            $table->bigInteger('grade_id')->unsigned()->nullable()->constrained('grades')->onDelete('set null');
            $table->bigInteger('academic_year_id')->unsigned()->nullable()->constrained('academic_years')->onDelete('set null');
            $table->decimal('amount', 12, 2)->nullable();
            $table->enum('period', ['monthly', 'semester', 'yearly', 'one_time'])->default('one_time');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_settings');
    }
};