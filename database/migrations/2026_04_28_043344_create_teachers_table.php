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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id('teacher_id');
            $table->unsignedBigInteger('user_id')->unique();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->string('nip')->unique();
            $table->string('nik')->unique();
            $table->string('full_name');
            $table->string('birth_place');
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female']);
            $table->string('religion');
            $table->string('address');
            $table->string('phone')->unique();
            $table->string('email')->unique();
            $table->enum('employment_status', ['permanent', 'contract']);
            $table->string('position');
            $table->string('grade_level');
            $table->enum('education_level', ['bachelor', 'master', 'doctorate']);
            $table->string('major');
            $table->string('certification');
            $table->string('npwp');
            $table->date('join_date');
            $table->enum('status', ['active', 'inactive', 'retired', 'suspended']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
