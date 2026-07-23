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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id('room_id');

            $table->unsignedBigInteger('school_id');

            $table->foreign('school_id')
                ->references('school_id')
                ->on('school_profiles')
                ->cascadeOnDelete();

            $table->string('room_name');
            $table->string('code');

            $table->enum('type', [
                'classroom',
                'lab',
                'library',
                'office',
                'sport',
            ]);

            $table->tinyInteger('floor')->unsigned();

            $table->string('building');

            $table->tinyInteger('capacity')->unsigned();

            $table->string('facility')->nullable();

            $table->enum('status', [
                'available',
                'maintenance',
                'inactive',
            ])->default('available');

            $table->timestamps();

            $table->softDeletes();

            $table->unique(['school_id', 'room_name']);
            $table->unique(['school_id', 'code']);

            $table->index('school_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
