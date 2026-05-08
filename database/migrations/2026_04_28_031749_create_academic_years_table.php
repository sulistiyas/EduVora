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
        Schema::create('academic_years', function (Blueprint $table) {
            // $table->id('academic_year_id');
            // $table->bigInteger('school_id')->unsigned();
            // $table->foreign('school_id')->references('school_id')->on('school_profiles')->onDelete('cascade');
            // $table->string('academic_year_name')->unique();
            // $table->date('start_date');
            // $table->date('end_date');
            // $table->enum('status',['active', 'inactive'])->default('inactive');
            // $table->timestamps();
            $table->id('academic_year_id');

            $table->bigInteger('school_id')->unsigned();
            $table->foreign('school_id')
                ->references('school_id')
                ->on('school_profiles')
                ->onDelete('cascade');

            $table->string('academic_year_name');

            $table->date('start_date');
            $table->date('end_date');

            $table->enum('status',['active', 'inactive'])
                ->default('inactive');

            $table->timestamps();

            // unique per school
            $table->unique(['school_id', 'academic_year_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};