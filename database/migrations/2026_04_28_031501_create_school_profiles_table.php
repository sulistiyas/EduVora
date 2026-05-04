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
        Schema::create('school_profiles', function (Blueprint $table) {
            $table->id('school_id');
            $table->string('school_name');
            $table->string('npsn')->unique()->nullable();
            $table->string('nss')->unique()->nullable();
            $table->enum('accreditation', ['A', 'B', 'C', 'D', 'E'])->nullable();
            $table->enum('school_type', ['Elementary', 'Junior High', 'Senior High'])->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('website')->nullable();
            $table->string('address')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('logo')->nullable();
            $table->string('headmaster_name')->nullable();
            $table->string('headmaster_nip')->nullable();
            $table->tinyInteger('kkm_default')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_profiles');
    }
};